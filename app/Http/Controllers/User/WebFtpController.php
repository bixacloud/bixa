<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HostingAccount;
use App\Models\WebFtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ZipArchive;
use Exception;

class WebFtpController extends Controller
{
    /**
     * FTP connection details
     */
    protected $connection;
    protected $isConnected = false;
    protected $account;
    protected $settings;
    protected $tempDir;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settings = WebFtpSetting::getSettings();
    }
    
    /**
     * Create temporary directory if it doesn't exist
     * 
     * @param int $userId User ID
     * @return string Temporary directory path
     */
    protected function ensureTempDirectoryExists($userId)
    {
        $tempDir = storage_path('app/temp/webftp/' . $userId);
        
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }
        
        if (!File::exists(storage_path('app/temp/webftp'))) {
            File::makeDirectory(storage_path('app/temp/webftp'), 0755, true);
        }
        
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
            Log::info('Created temp directory for user', [
                'user_id' => $userId,
                'temp_dir' => $tempDir
            ]);
        }
        
        return $tempDir;
    }

  /**
 * Show Web FTP interface with support for root directory access
 *
 * @param string $username Hosting account username
 * @param string $path Directory path (optional)
 * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
 */
public function index($username, $path = '/')
{
    try {
        // Get user ID from Auth facade
        $userId = Auth::id();
        
        // Debug logging
        Log::info('WebFTP Index called', [
            'username' => $username,
            'path' => $path,
            'user_id' => $userId,
            'full_url' => request()->fullUrl()
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            Log::error('User not authenticated');
            return redirect()->route('login')
                ->with('error', 'Please login to use WebFTP.');
        }

        // Check if web FTP is enabled
        if (!WebFtpSetting::isEnabled()) {
            return $this->redirectToExternalFileManager($username, $userId);
        }

        // Ensure temp directory exists
        $this->tempDir = $this->ensureTempDirectoryExists($userId);

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->first();
                
        if (!$account) {
            Log::error('Account not found', [
                'username' => $username,
                'user_id' => $userId
            ]);
            return redirect()->route('hosting.index')
                ->with('error', 'Hosting account not found.');
        }

        if ($account->status !== 'active') {
            return redirect()->route('hosting.view', $username)
                ->with('error', 'Account must be active to use Web FTP.');
        }

        // Log the path before transformation
        Log::info('WebFTP index: Path before transformation', [
            'original_path' => $path
        ]);
        
        // Use new handlePath method that respects root access
        $ftpPath = $this->handlePath($path);
        
        Log::info('WebFTP index: Path after transformation', [
            'original_path' => $path,
            'ftp_path' => $ftpPath,
            'is_root' => $ftpPath === '/'
        ]);

        // Connect to FTP
        $connection = $this->connect($account);

        if (!$connection['success']) {
            return redirect()->route('hosting.view', $username)
                ->with('error', $connection['message']);
        }

        $this->account = $account;
        
        // Get directory listing
        $listing = $this->listDirectory($ftpPath);
        
        // Close connection
        $this->disconnect();

        // Handle path for breadcrumbs using the updated method
        $pathParts = $this->getPathParts($ftpPath);
        
        Log::info('WebFTP index: Ready to display directory', [
            'current_path' => $ftpPath,
            'items_count' => count($listing)
        ]);
        
        return view('hosting.webftp.index', [
            'account' => $account,
            'currentPath' => $ftpPath,
            'pathParts' => $pathParts,
            'listing' => $listing,
            'settings' => $this->settings,
            'isRootDirectory' => $ftpPath === '/' // Add this flag to indicate when we're at root
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Error: ' . $e->getMessage(), [
            'username' => $username,
            'path' => $path,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('hosting.view', $username)
            ->with('error', 'Error accessing FTP: ' . $e->getMessage());
    }
}

    /**
     * Connect to FTP server
     *
     * @param HostingAccount $account
     * @return array Connection result
     */
    protected function connect(HostingAccount $account)
    {
        try {
            Log::debug('WebFTP connect: Connecting to FTP server', [
                'username' => $account->username,
                'host' => 'ftpupload.net'
            ]);
            
            $this->connection = ftp_connect('ftpupload.net', 21, 30);
            
            if (!$this->connection) {
                Log::error('WebFTP connect: Connection failed');
                return [
                    'success' => false,
                    'message' => 'Could not connect to FTP server'
                ];
            }

            Log::debug('WebFTP connect: Attempting login');
            $login = ftp_login($this->connection, $account->username, $account->password);
            
            if (!$login) {
                Log::error('WebFTP connect: Login failed', [
                    'username' => $account->username
                ]);
                ftp_close($this->connection);
                return [
                    'success' => false,
                    'message' => 'Invalid FTP credentials'
                ];
            }

            // Set passive mode
            ftp_pasv($this->connection, true);
            
            $currentDir = ftp_pwd($this->connection);
            Log::debug('WebFTP connect: Connected successfully', [
                'current_directory' => $currentDir
            ]);
            
            $this->isConnected = true;
            
            return [
                'success' => true,
                'message' => 'Connected successfully'
            ];

        } catch (Exception $e) {
            Log::error('WebFTP connect: FTP connection error: ' . $e->getMessage(), [
                'username' => $account->username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect from FTP server
     */
    protected function disconnect()
    {
        if ($this->isConnected && $this->connection) {
            ftp_close($this->connection);
            $this->isConnected = false;
            Log::debug('WebFTP disconnect: Connection closed');
        }
    }

    /**
     * List directory contents
     *
     * @param string $path Directory path
     * @return array Directory listing
     */
    protected function listDirectory($path)
    {
        try {
            Log::debug('WebFTP listDirectory: Listing directory', [
                'path' => $path
            ]);
            
            $rawList = ftp_rawlist($this->connection, $path);
            
            if (!$rawList) {
                Log::warning('WebFTP listDirectory: Empty or invalid directory', [
                    'path' => $path
                ]);
                return [];
            }
            
            $items = [];
            
            foreach ($rawList as $item) {
                $parsedItem = $this->parseListItem($item, $path);
                if ($parsedItem) {
                    $items[] = $parsedItem;
                }
            }

            // Sort directories first, then files
            usort($items, function($a, $b) {
                if ($a['is_dir'] && !$b['is_dir']) return -1;
                if (!$a['is_dir'] && $b['is_dir']) return 1;
                return strcasecmp($a['name'], $b['name']);
            });
            
            Log::debug('WebFTP listDirectory: Found items', [
                'count' => count($items),
                'path' => $path
            ]);
            
            return $items;

        } catch (Exception $e) {
            Log::error('Error listing directory: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Parse a list item
     *
     * @param string $item Raw list item
     * @param string $currentPath Current path
     * @return array|null Parsed item details
     */
    protected function parseListItem($item, $currentPath)
    {
        $vinfo = preg_split("/[\s]+/", $item, 9);
        
        if (!isset($vinfo[8])) {
            return null;
        }
        
        $permissions = $vinfo[0];
        $is_dir = $permissions[0] === 'd';
        $size = $vinfo[4];
        $name = $vinfo[8];
        
        // Skip current and parent directory entries
        if ($name === '.' || $name === '..') {
            return null;
        }
        
        // Format size
        $formatted_size = $this->formatSize($size);
        
        // Determine file type and icon
        $type = $is_dir ? 'dir' : $this->getFileType($name);
        $icon = $this->getFileIcon($type);

        // Path handling
        $path = $currentPath;
        if ($path !== '/' && !Str::endsWith($path, '/')) {
            $path .= '/';
        }
        
        $fullPath = $path . $name;
        if ($is_dir && !Str::endsWith($fullPath, '/')) {
            $fullPath .= '/';
        }
        
        return [
            'name' => $name,
            'is_dir' => $is_dir,
            'size' => $size,
            'formatted_size' => $formatted_size,
            'type' => $type,
            'icon' => $icon,
            'path' => $fullPath,
            'timestamp' => $this->parseTimestamp($vinfo[5], $vinfo[6], $vinfo[7]),
            'permissions' => $permissions
        ];
    }

    /**
     * Format file size
     *
     * @param int $size Size in bytes
     * @return string Formatted size
     */
    protected function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = max($size, 0);
        $pow = floor(($size ? log($size) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $size /= pow(1024, $pow);
        
        return round($size, 2) . ' ' . $units[$pow];
    }

    /**
     * Parse file timestamp
     *
     * @param string $month Month
     * @param string $day Day
     * @param string $yearOrTime Year or time
     * @return int Unix timestamp
     */
    protected function parseTimestamp($month, $day, $yearOrTime)
    {
        $months = [
            'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
            'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
        ];
        
        $month = $months[$month] ?? 1;
        
        // If year is provided
        if (strpos($yearOrTime, ':') === false) {
            return mktime(0, 0, 0, $month, (int)$day, (int)$yearOrTime);
        }
        
        // If time is provided (assume current year)
        list($hour, $minute) = explode(':', $yearOrTime);
        $year = date('Y');
        
        return mktime((int)$hour, (int)$minute, 0, $month, (int)$day, $year);
    }

    /**
     * Get file type from extension
     *
     * @param string $filename Filename
     * @return string File type
     */
    protected function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $types = [
            'html' => 'html',
            'htm' => 'html',
            'php' => 'php',
            'css' => 'css',
            'js' => 'javascript',
            'json' => 'json',
            'txt' => 'text',
            'md' => 'markdown',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'svg' => 'image',
            'pdf' => 'pdf',
            'zip' => 'archive',
            'rar' => 'archive',
            'tar' => 'archive',
            'gz' => 'archive',
            'sql' => 'sql',
            'xml' => 'xml',
            'log' => 'log'
        ];
        
        return $types[$extension] ?? 'unknown';
    }

    /**
     * Get file icon based on type
     *
     * @param string $type File type
     * @return string Icon identifier
     */
    protected function getFileIcon($type)
    {
        $icons = [
            'dir' => 'folder',
            'html' => 'code',
            'php' => 'code',
            'css' => 'code',
            'javascript' => 'code',
            'json' => 'code',
            'text' => 'file-text',
            'markdown' => 'file-text',
            'image' => 'image',
            'pdf' => 'file-pdf',
            'archive' => 'archive',
            'sql' => 'database',
            'xml' => 'code',
            'log' => 'file-text',
            'unknown' => 'file'
        ];
        
        return $icons[$type] ?? 'file';
    }

    /**
     * Remove /htdocs prefix from path for URL usage
     *
     * @param string $path Path with possible /htdocs prefix
     * @return string Path without /htdocs prefix
     */
    protected function removeHtdocsPrefix($path)
    {
        Log::debug('WebFTP removeHtdocsPrefix: Processing path', [
            'input_path' => $path
        ]);
        
        $result = ltrim(str_replace('/htdocs', '', $path), '/');
        
        Log::debug('WebFTP removeHtdocsPrefix: Result', [
            'output_path' => $result
        ]);
        
        return $result;
    }

    

    /**
     * Sanitize path
     *
     * @param string $path Path to sanitize
     * @return string Sanitized path
     */
    protected function sanitizePath($path)
    {
        Log::debug('WebFTP sanitizePath: Processing path', [
            'input_path' => $path
        ]);
        
        // Ensure path starts with a slash
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        
        // Remove any double slashes
        $path = preg_replace('#/+#', '/', $path);
        
        // Remove trailing slash unless it's the root
        if ($path !== '/' && Str::endsWith($path, '/')) {
            $path = rtrim($path, '/');
        }
        
        Log::debug('WebFTP sanitizePath: Result', [
            'output_path' => $path
        ]);
        
        return $path;
    }

   

    /**
     * Show file editor
     *
     * @param string $username Hosting account username
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($username, Request $request)
    {
        try {
            // Lấy path từ query string
            $path = $request->query('path');
            
            // Log thông tin request
            Log::info('WebFTP edit called with query param:', [
                'username' => $username,
                'path' => $path,
                'request_path' => request()->path(),
                'full_url' => request()->fullUrl(),
                'method' => request()->method(),
                'query_string' => request()->getQueryString()
            ]);
            
            // Get user ID
            $userId = Auth::id();
            
            // Check if user is authenticated
            if (!$userId) {
                return redirect()->route('login')
                    ->with('error', 'Please login to use WebFTP.');
            }
            
            // Check if web FTP is enabled
            if (!WebFtpSetting::isEnabled()) {
                return $this->redirectToExternalFileManager($username, $userId);
            }

            // Ensure temp directory exists
            $this->tempDir = $this->ensureTempDirectoryExists($userId);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return redirect()->route('hosting.view', $username)
                    ->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure path starts with /htdocs for FTP operations
            $ftpPath = $this->ensureHtdocsPath($path);
            
            Log::info('WebFTP edit: Path after transformation', [
                'original_path' => $path,
                'ftp_path' => $ftpPath
            ]);
            
            $pathParts = $this->getPathParts($ftpPath);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return redirect()->route('hosting.view', $username)
                    ->with('error', $connection['message']);
            }
            
            // Generate a temp filename
            $filename = basename($ftpPath);
            $tempFile = $this->tempDir . '/' . Str::random(16) . '_' . $filename;
            
            Log::info('WebFTP edit: Attempting to download file', [
                'ftp_path' => $ftpPath,
                'temp_file' => $tempFile
            ]);
            
            // Download the file
            if (!ftp_get($this->connection, $tempFile, $ftpPath, FTP_BINARY)) {
                $error = error_get_last();
                Log::error('WebFTP edit: Failed to download file', [
                    'ftp_path' => $ftpPath,
                    'temp_file' => $tempFile,
                    'php_error' => $error
                ]);
                
                $this->disconnect();
                return redirect()->route('webftp.index', [$username, dirname($path)])
                    ->with('error', 'Failed to download file for editing.');
            }
            
            Log::info('WebFTP edit: File downloaded successfully', [
                'temp_file' => $tempFile,
                'file_size' => File::size($tempFile)
            ]);
            
            // Get file content
            $content = File::get($tempFile);
            
            // Close connection
            $this->disconnect();
            
            // Determine file type for editor mode
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $editorMode = $this->getEditorMode($extension);
            
            // Store temp file path in session for use when saving
            session(['webftp_temp_file' => $tempFile]);
            session(['webftp_remote_path' => $ftpPath]);
            
            Log::info('WebFTP edit: Ready to display editor', [
                'filename' => $filename,
                'path' => $ftpPath,
                'editor_mode' => $editorMode,
                'content_length' => strlen($content)
            ]);
            
            return view('hosting.webftp.editor', [
                'account' => $account,
                'filename' => $filename,
                'path' => $ftpPath,
                'pathParts' => $pathParts,
                'content' => $content,
                'editorMode' => $editorMode,
                'settings' => $this->settings
            ]);

        } catch (Exception $e) {
            Log::error('WebFTP Edit Error: ' . $e->getMessage(), [
                'username' => $username,
                'path' => $path ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('webftp.index', [$username])
                ->with('error', 'Error editing file: ' . $e->getMessage());
        }
    }

    /**
     * Get editor mode based on file extension
     *
     * @param string $extension File extension
     * @return string Editor mode
     */
    protected function getEditorMode($extension)
    {
        $modes = [
            'html' => 'html',
            'htm' => 'html',
            'php' => 'php',
            'css' => 'css',
            'js' => 'javascript',
            'json' => 'json',
            'txt' => 'text',
            'md' => 'markdown',
            'sql' => 'sql',
            'xml' => 'xml',
            'sh' => 'shell',
            'py' => 'python',
            'rb' => 'ruby',
            'java' => 'java',
            'c' => 'c_cpp',
            'cpp' => 'c_cpp',
            'h' => 'c_cpp',
            'cs' => 'csharp',
            'yml' => 'yaml',
            'yaml' => 'yaml',
            'ini' => 'ini'
        ];
        
        return $modes[$extension] ?? 'text';
    }

    /**
     * Save file after editing
     *
     * @param Request $request
     * @param string $username Hosting account username
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFile(Request $request, $username)
    {
        try {
            // Get user ID
            $userId = Auth::id();
            
            Log::info('WebFTP saveFile: Start saving file', [
                'username' => $username,
                'user_id' => $userId
            ]);
            
            // Check if user is authenticated
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to use WebFTP.'
                ]);
            }
            
            // Validate request
            $request->validate([
                'content' => 'required'
            ]);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account must be active to use Web FTP.'
                ]);
            }

            // Get temp file and remote path from session
            $tempFile = session('webftp_temp_file');
            $remotePath = session('webftp_remote_path');
            
            Log::info('WebFTP saveFile: Session data retrieved', [
                'temp_file' => $tempFile,
                'remote_path' => $remotePath
            ]);
            
            if (!$tempFile || !$remotePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please reopen the file.'
                ]);
            }
            
            // Check if temp file exists
            if (!File::exists($tempFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Temporary file not found. Please reopen the file.'
                ]);
            }
            
            // Save content to temp file
            File::put($tempFile, $request->content);
            
            Log::info('WebFTP saveFile: Content saved to temp file', [
                'temp_file' => $tempFile,
                'content_size' => strlen($request->content)
            ]);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $connection['message']
                ]);
            }
            
            // Upload the file
            if (!ftp_put($this->connection, $remotePath, $tempFile, FTP_BINARY)) {
                $this->disconnect();
                Log::error('WebFTP saveFile: Failed to upload file', [
                    'remote_path' => $remotePath,
                    'temp_file' => $tempFile
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file to server.'
                ]);
            }
            
            Log::info('WebFTP saveFile: File uploaded successfully', [
                'remote_path' => $remotePath
            ]);
            
            // Close connection
            $this->disconnect();
            
            return response()->json([
                'success' => true,
                'message' => 'File saved successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('WebFTP Save Error: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving file: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download file
     *
     * @param string $username Hosting account username
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($username, Request $request)
    {
        try {
            // Lấy path từ query string
            $path = $request->query('path');
            
            // Log thông tin request
            Log::info('WebFTP download: Start downloading file', [
                'username' => $username,
                'path' => $path,
                'full_url' => request()->fullUrl(),
                'query_string' => request()->getQueryString()
            ]);
            
            // Get user ID
            $userId = Auth::id();
            
            // Check if user is authenticated
            if (!$userId) {
                return back()->with('error', 'Please login to use WebFTP.');
            }
            
            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return back()->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure path starts with /htdocs
            $ftpPath = $this->ensureHtdocsPath($path);
            $filename = basename($ftpPath);
            
            Log::info('WebFTP download: Path prepared', [
                'original_path' => $path,
                'ftp_path' => $ftpPath,
                'filename' => $filename
            ]);
            
            // Ensure temp directory exists
            $this->tempDir = $this->ensureTempDirectoryExists($userId);
            
            // Generate a temp filename
            $tempFile = $this->tempDir . '/' . Str::random(16) . '_' . $filename;
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return back()->with('error', $connection['message']);
            }
            
            // Download the file
            if (!ftp_get($this->connection, $tempFile, $ftpPath, FTP_BINARY)) {
                $this->disconnect();
                Log::error('WebFTP download: Failed to download file', [
                    'ftp_path' => $ftpPath,
                    'temp_file' => $tempFile
                ]);
                return back()->with('error', 'Failed to download file.');
            }
            
            Log::info('WebFTP download: File downloaded successfully', [
                'temp_file' => $tempFile,
                'size' => filesize($tempFile)
            ]);
            
            // Close connection
            $this->disconnect();
            
            // Serve file for download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/octet-stream',
            ])->deleteFileAfterSend(true);

        } catch (Exception $e) {
            Log::error('WebFTP Download Error: ' . $e->getMessage(), [
                'username' => $username,
                'path' => $path ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }

   

/**
 * Upload file with AJAX support for drag & drop
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @param string $path Directory path
 * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
 */
public function upload(Request $request, $username, $path)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        Log::info('WebFTP upload: Start upload', [
            'username' => $username,
            'path' => $path,
            'user_id' => $userId,
            'ajax' => $request->ajax()
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to use WebFTP.'
                ]);
            }
            return back()->with('error', 'Please login to use WebFTP.');
        }
        
        // Validate request
        $maxSize = ($this->settings->max_upload_size ?? 10) * 1024;
        
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:' . $maxSize
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account must be active to use Web FTP.'
                ]);
            }
            return back()->with('error', 'Account must be active to use Web FTP.');
        }

        // Ensure path starts with /htdocs
        $ftpPath = $this->ensureHtdocsPath($path);
        
        Log::info('WebFTP upload: FTP path prepared', [
            'original_path' => $path,
            'ftp_path' => $ftpPath
        ]);
        
        // Get uploaded file
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        
        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        
        Log::info('WebFTP upload: File details', [
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
        
        // Connect to FTP
        $connection = $this->connect($account);

        if (!$connection['success']) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $connection['message']
                ]);
            }
            return back()->with('error', $connection['message']);
        }
        
        // Change to target directory
        if (!ftp_chdir($this->connection, $ftpPath)) {
            $this->disconnect();
            Log::error('WebFTP upload: Failed to change to target directory', [
                'ftp_path' => $ftpPath
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to access target directory.'
                ]);
            }
            return back()->with('error', 'Failed to change to target directory.');
        }
        
        // Check if file already exists and generate a unique name if needed
        $existingFiles = ftp_nlist($this->connection, '.');
        $originalFilename = $filename;
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        $counter = 1;
        
        while (in_array($filename, $existingFiles)) {
            $filename = $baseFilename . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        // Upload the file
        if (!ftp_put($this->connection, $filename, $file->getPathname(), FTP_BINARY)) {
            $this->disconnect();
            Log::error('WebFTP upload: Failed to upload file', [
                'filename' => $filename
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file to server.'
                ]);
            }
            return back()->with('error', 'Failed to upload file to server.');
        }
        
        Log::info('WebFTP upload: File uploaded successfully', [
            'filename' => $filename,
            'ftp_path' => $ftpPath,
            'renamed' => ($originalFilename !== $filename)
        ]);
        
        // Close connection
        $this->disconnect();
        
        // Return response based on request type
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully' . ($originalFilename !== $filename ? ' as ' . $filename : ''),
                'filename' => $filename
            ]);
        }
        
        return back()->with('success', 'File uploaded successfully' . ($originalFilename !== $filename ? ' as ' . $filename : ''));

    } catch (Exception $e) {
        Log::error('WebFTP Upload Error: ' . $e->getMessage(), [
            'username' => $username,
            'path' => $path,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ]);
        }
        
        return back()->with('error', 'Error uploading file: ' . $e->getMessage());
    }
}

    /**
     * Create new directory
     * 
     * @param Request $request
     * @param string $username Hosting account username
     * @param string $path Parent directory path (optional)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createDirectory(Request $request, $username, $path = '/')
    {
        try {
            Log::info('WebFTP createDirectory: Start creating directory', [
                'username' => $username,
                'path' => $path,
                'user_id' => Auth::id()
            ]);
            
            // Validate request
            $request->validate([
                'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+$/'
            ]);

            // Get current user ID
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login')
                    ->with('error', 'Please login to use WebFTP.');
            }

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->first();
                
            if (!$account) {
                return redirect()->route('hosting.index')
                    ->with('error', 'Hosting account not found.');
            }

            if ($account->status !== 'active') {
                return back()->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure path starts with /htdocs
            $ftpPath = $this->ensureHtdocsPath($path);
            
            Log::info('WebFTP createDirectory: FTP path prepared', [
                'original_path' => $path,
                'ftp_path' => $ftpPath,
                'directory_name' => $request->name
            ]);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return back()->with('error', $connection['message']);
            }
            
            // Create new directory
            $newDir = $ftpPath . '/' . $request->name;
            $newDir = $this->sanitizePath($newDir);
            
            if (!ftp_mkdir($this->connection, $newDir)) {
                $this->disconnect();
                Log::error('WebFTP createDirectory: Failed to create directory', [
                    'new_dir' => $newDir
                ]);
                return back()->with('error', 'Failed to create directory.');
            }
            
            Log::info('WebFTP createDirectory: Directory created successfully', [
                'new_dir' => $newDir
            ]);
            
            // Close connection
            $this->disconnect();
            
            return back()->with('success', 'Directory created successfully.');

        } catch (Exception $e) {
            Log::error('WebFTP Create Directory Error: ' . $e->getMessage(), [
                'username' => $username,
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error creating directory: ' . $e->getMessage());
        }
    }

    /**
     * Create new file
     * 
     * @param Request $request
     * @param string $username Hosting account username
     * @param string $path Directory path
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFile(Request $request, $username, $path = '/')
    {
        // Start logging for debugging
        Log::info('WebFTP createFile started', [
            'username' => $username,
            'path' => $path,
            'user_id' => Auth::id(),
            'method' => $request->method(),
            'route' => $request->route()->getName()
        ]);
        
        try {
            // Validate input
            $request->validate([
                'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+$/'
            ]);
            
            Log::info('WebFTP createFile: Name validation passed', [
                'file_name' => $request->name
            ]);

            // Get current user ID
            $userId = Auth::id();
            if (!$userId) {
                Log::warning('WebFTP createFile: User not authenticated');
                return redirect()->route('login')
                    ->with('error', 'Please login to use WebFTP.');
            }

            // Find hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->first();
                
            if (!$account) {
                Log::error('WebFTP createFile: Account not found', [
                    'username' => $username,
                    'user_id' => $userId
                ]);
                return redirect()->route('hosting.index')
                    ->with('error', 'Hosting account not found.');
            }

            // Check account status
            if ($account->status !== 'active') {
                Log::warning('WebFTP createFile: Account not active', [
                    'status' => $account->status
                ]);
                return back()->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure path starts with /htdocs for FTP operations
            $ftpPath = $this->ensureHtdocsPath($path);
            Log::info('WebFTP createFile: Path with htdocs prepared', [
                'original_path' => $path,
                'ftp_path' => $ftpPath
            ]);
            
            // Prepare temp directory
            $tempDir = storage_path('app/temp/webftp/' . $userId);
            
            // Create temp directory if not exists
            if (!File::exists(storage_path('app/temp'))) {
                File::makeDirectory(storage_path('app/temp'), 0755, true);
            }
            
            if (!File::exists(storage_path('app/temp/webftp'))) {
                File::makeDirectory(storage_path('app/temp/webftp'), 0755, true);
            }
            
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            
            Log::info('WebFTP createFile: Temp directory ready', ['tempDir' => $tempDir]);
            
            // Create empty temp file
            $tempFile = $tempDir . '/' . Str::random(16) . '_temp.txt';
            File::put($tempFile, '');
            
            Log::info('WebFTP createFile: Temp file created', ['tempFile' => $tempFile]);
            
            // Connect FTP
            $connection = ftp_connect('ftpupload.net', 21, 30);
            if (!$connection) {
                File::delete($tempFile);
                Log::error('WebFTP createFile: FTP connection failed');
                return back()->with('error', 'Could not connect to FTP server');
            }
            
            // Login FTP
            $login = ftp_login($connection, $account->username, $account->password);
            if (!$login) {
                ftp_close($connection);
                File::delete($tempFile);
                Log::error('WebFTP createFile: FTP login failed');
                return back()->with('error', 'Invalid FTP credentials');
            }
            
            // Set passive mode
            ftp_pasv($connection, true);
            
            Log::info('WebFTP createFile: FTP connected successfully');
            
            // Build new file path for FTP
            $newFtpFile = $ftpPath . '/' . $request->name;
            $newFtpFile = $this->sanitizePath($newFtpFile);
            
            Log::info('WebFTP createFile: Preparing to create file', ['newFtpFile' => $newFtpFile]);
            
            // Upload empty file
            $result = false;
            try {
                $result = ftp_put($connection, $newFtpFile, $tempFile, FTP_BINARY);
            } catch (Exception $e) {
                Log::error('WebFTP createFile: FTP put exception', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Close FTP connection
            ftp_close($connection);
            
            // Delete temp file
            File::delete($tempFile);
            
            // Check upload result
            if (!$result) {
                Log::error('WebFTP createFile: Failed to upload file', [
                    'newFtpFile' => $newFtpFile
                ]);
                return back()->with('error', 'Failed to create file on server.');
            }
            
            Log::info('WebFTP createFile: File created successfully', ['newFtpFile' => $newFtpFile]);
            
            // ĐÃ THAY ĐỔI: Sử dụng query param thay vì path parameter
            $urlPath = $this->removeHtdocsPrefix($newFtpFile);
            
            Log::info('WebFTP createFile: URL path prepared', [
                'original_path' => $newFtpFile,
                'url_path' => $urlPath
            ]);
            
            // Tạo URL cho trang edit với query parameter
            $redirectUrl = route('webftp.edit', [
                'username' => $username
            ]) . '?path=' . urlencode($urlPath);
            
            Log::info('WebFTP createFile: Redirecting to edit page', [
                'redirect_url' => $redirectUrl
            ]);
            
            return redirect($redirectUrl)->with('success', 'File created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('WebFTP createFile: Validation error', [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('WebFTP createFile: Error', [
                'username' => $username,
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Delete temp file if still exists
            if (isset($tempFile) && File::exists($tempFile)) {
                File::delete($tempFile);
            }

            return back()->with('error', 'Error creating file: ' . $e->getMessage());
        }
    }

    /**
     * Rename file or directory
     *
     * @param Request $request
     * @param string $username Hosting account username
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(Request $request, $username)
    {
        try {
            // Get user ID
            $userId = Auth::id();
            
            Log::info('WebFTP rename: Start renaming', [
                'username' => $username,
                'user_id' => $userId
            ]);
            
            // Check if user is authenticated
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to use WebFTP.'
                ]);
            }
            
            // Validate request
            $request->validate([
                'path' => 'required|string',
                'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+$/'
            ]);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account must be active to use Web FTP.'
                ]);
            }

            // Sanitize paths
            $oldPath = $this->ensureHtdocsPath($request->path);
            $dirPath = dirname($oldPath);
            $newPath = $dirPath . '/' . $request->name;
            $newPath = $this->sanitizePath($newPath);
            
            Log::info('WebFTP rename: Paths prepared', [
                'old_path' => $oldPath,
                'new_path' => $newPath,
                'dir_path' => $dirPath
            ]);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $connection['message']
                ]);
            }
            
            // Rename file or directory
            if (!ftp_rename($this->connection, $oldPath, $newPath)) {
                $this->disconnect();
                Log::error('WebFTP rename: Failed to rename item', [
                    'old_path' => $oldPath,
                    'new_path' => $newPath
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to rename item.'
                ]);
            }
            
            Log::info('WebFTP rename: Item renamed successfully', [
                'old_path' => $oldPath,
                'new_path' => $newPath
            ]);
            
            // Close connection
            $this->disconnect();
            
            return response()->json([
                'success' => true,
                'message' => 'Item renamed successfully.',
                'new_path' => $newPath
            ]);

        } catch (Exception $e) {
            Log::error('WebFTP Rename Error: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error renaming item: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete file or directory
     *
     * @param Request $request
     * @param string $username Hosting account username
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $username)
    {
        try {
            // Get user ID
            $userId = Auth::id();
            
            Log::info('WebFTP delete: Start deleting', [
                'username' => $username,
                'user_id' => $userId
            ]);
            
            // Check if user is authenticated
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to use WebFTP.'
                ]);
            }
            
            // Validate request
            $request->validate([
                'path' => 'required|string',
                'is_dir' => 'required|boolean'
            ]);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account must be active to use Web FTP.'
                ]);
            }

            // Ensure path starts with /htdocs
            $path = $this->ensureHtdocsPath($request->path);
            
            Log::info('WebFTP delete: Path prepared', [
                'path' => $path,
                'is_dir' => $request->is_dir
            ]);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $connection['message']
                ]);
            }
            
            // Delete file or directory
            if ($request->is_dir) {
                $result = $this->deleteDirectory($path);
            } else {
                $result = ftp_delete($this->connection, $path);
            }
            
            // Close connection
            $this->disconnect();
            
            if (!$result) {
                Log::error('WebFTP delete: Failed to delete item', [
                    'path' => $path,
                    'is_dir' => $request->is_dir
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete item.'
                ]);
            }
            
            Log::info('WebFTP delete: Item deleted successfully', [
                'path' => $path,
                'is_dir' => $request->is_dir
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('WebFTP Delete Error: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete directory recursively
     *
     * @param string $path Directory path
     * @return bool Success status
     */
    protected function deleteDirectory($path)
    {
        try {
            Log::debug('WebFTP deleteDirectory: Starting recursive deletion', [
                'path' => $path
            ]);
            
            $list = ftp_rawlist($this->connection, $path);
            
            if (!$list) {
                Log::debug('WebFTP deleteDirectory: Empty directory, deleting', [
                    'path' => $path
                ]);
                return ftp_rmdir($this->connection, $path);
            }
            
            foreach ($list as $item) {
                $parsedItem = $this->parseListItem($item, $path);
                
                if (!$parsedItem) {
                    continue;
                }
                
                $itemPath = $parsedItem['path'];
                
                if ($parsedItem['is_dir']) {
                    Log::debug('WebFTP deleteDirectory: Deleting subdirectory', [
                        'path' => $itemPath
                    ]);
                    $this->deleteDirectory($itemPath);
                } else {
                    Log::debug('WebFTP deleteDirectory: Deleting file', [
                        'path' => $itemPath
                    ]);
                    ftp_delete($this->connection, $itemPath);
                }
            }
            
            Log::debug('WebFTP deleteDirectory: Deleting directory after contents removed', [
                'path' => $path
            ]);
            return ftp_rmdir($this->connection, $path);
            
        } catch (Exception $e) {
            Log::error('Error deleting directory: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Check if a path is a directory
     *
     * @param string $path Path to check
     * @return bool True if directory
     */
    protected function isDirectory($path)
    {
        try {
            $list = ftp_rawlist($this->connection, $path);
            if ($list === false) {
                return false;
            }
            
            // Check if it's a directory
            $current = ftp_pwd($this->connection);
            $result = ftp_chdir($this->connection, $path);
            
            if ($result) {
                // Change back to previous directory
                ftp_chdir($this->connection, $current);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if a directory exists
     *
     * @param string $path Directory path
     * @return bool True if exists
     */
    protected function directoryExists($path)
    {
        try {
            $current = ftp_pwd($this->connection);
            $result = @ftp_chdir($this->connection, $path);
            
            if ($result) {
                ftp_chdir($this->connection, $current);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create remote directories recursively
     *
     * @param string $path Directory path
     * @return bool Success status
     */
    protected function createRemoteDirectories($path)
    {
        if ($path === '/' || $path === '/htdocs') {
            return true;
        }
        
        if ($this->directoryExists($path)) {
            return true;
        }
        
        // Create parent directory first
        $parent = dirname($path);
        $this->createRemoteDirectories($parent);
        
        // Create directory
        return @ftp_mkdir($this->connection, $path);
    }

    /**
     * Zip files and directories
     *
     * @param Request $request
     * @param string $username Hosting account username
     * @param string $path Directory path
     * @return \Illuminate\Http\RedirectResponse
     */
    public function zipFiles(Request $request, $username, $path)
    {
        try {
            // Get user ID
            $userId = Auth::id();
            
            Log::info('WebFTP zipFiles: Start zipping files', [
                'username' => $username,
                'path' => $path,
                'user_id' => $userId
            ]);
            
            // Check if user is authenticated
            if (!$userId) {
                return back()->with('error', 'Please login to use WebFTP.');
            }
            
            // Check if zip operations are allowed
            if (!$this->settings->allow_zip_operations) {
                return back()->with('error', 'Zip operations are disabled.');
            }
            
            // Validate request
            $request->validate([
                'items' => 'required|array',
                'items.*' => 'required|string',
                'zip_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+\.zip$/'
            ]);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return back()->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure path starts with /htdocs
            $ftpPath = $this->ensureHtdocsPath($path);
            
            Log::info('WebFTP zipFiles: Path prepared', [
                'original_path' => $path,
                'ftp_path' => $ftpPath,
                'items_count' => count($request->items),
                'zip_name' => $request->zip_name
            ]);
            
            // Ensure temp directory exists
            $this->tempDir = $this->ensureTempDirectoryExists($userId);
            
            // Create temp zip directory
            $tempZipDir = $this->tempDir . '/zip_' . Str::random(8);
            File::makeDirectory($tempZipDir, 0755, true);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                File::deleteDirectory($tempZipDir);
                return back()->with('error', $connection['message']);
            }
            
            // Download selected items
            foreach ($request->items as $item) {
                $itemPath = $this->ensureHtdocsPath($item);
                $relativePath = str_replace($ftpPath . '/', '', $itemPath);
                
                Log::debug('WebFTP zipFiles: Processing item', [
                    'item_path' => $itemPath,
                    'relative_path' => $relativePath
                ]);
                
                // Check if it's a directory
                $isDir = $this->isDirectory($itemPath);
                
                if ($isDir) {
                    // Create the directory locally
                    $localDir = $tempZipDir . '/' . $relativePath;
                    File::makeDirectory($localDir, 0755, true);
                    
                    Log::debug('WebFTP zipFiles: Created local directory', [
                        'local_dir' => $localDir
                    ]);
                    
                    // Download directory contents recursively
                    $this->downloadDirectory($itemPath, $localDir, $ftpPath);
                } else {
                    // Create parent directories if needed
                    $parentDir = dirname($tempZipDir . '/' . $relativePath);
                    if (!File::exists($parentDir)) {
                        File::makeDirectory($parentDir, 0755, true);
                    }
                    
                    // Download the file
                    ftp_get($this->connection, $tempZipDir . '/' . $relativePath, $itemPath, FTP_BINARY);
                    
                    Log::debug('WebFTP zipFiles: Downloaded file', [
                        'file_path' => $itemPath,
                        'local_path' => $tempZipDir . '/' . $relativePath
                    ]);
                }
            }
            
            // Create zip file
            $zipFile = $this->tempDir . '/' . $request->zip_name;
            $zip = new ZipArchive();
            
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempZipDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                $fileCount = 0;
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $relativePath = substr($file->getPathname(), strlen($tempZipDir) + 1);
                        $zip->addFile($file->getPathname(), $relativePath);
                        $fileCount++;
                    }
                }
                
                $zip->close();
                
                Log::info('WebFTP zipFiles: Created zip file', [
                    'zip_file' => $zipFile,
                    'files_count' => $fileCount,
                    'zip_size' => filesize($zipFile)
                ]);
            } else {
                $this->disconnect();
                File::deleteDirectory($tempZipDir);
                Log::error('WebFTP zipFiles: Failed to create zip file');
                return back()->with('error', 'Failed to create zip file.');
            }
            
            // Upload zip file
            if (!ftp_put($this->connection, $ftpPath . '/' . $request->zip_name, $zipFile, FTP_BINARY)) {
                $this->disconnect();
                File::deleteDirectory($tempZipDir);
                File::delete($zipFile);
                Log::error('WebFTP zipFiles: Failed to upload zip file', [
                    'zip_file' => $zipFile,
                    'target_path' => $ftpPath . '/' . $request->zip_name
                ]);
                return back()->with('error', 'Failed to upload zip file.');
            }
            
            Log::info('WebFTP zipFiles: Uploaded zip file successfully', [
                'target_path' => $ftpPath . '/' . $request->zip_name
            ]);
            
            // Close connection
            $this->disconnect();
            
            // Clean up temp files
            File::deleteDirectory($tempZipDir);
            File::delete($zipFile);
            
            return back()->with('success', 'Files zipped successfully.');

        } catch (Exception $e) {
            Log::error('WebFTP Zip Error: ' . $e->getMessage(), [
                'username' => $username,
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up temp files if they exist
            if (isset($tempZipDir) && File::exists($tempZipDir)) {
                File::deleteDirectory($tempZipDir);
            }
            
            if (isset($zipFile) && File::exists($zipFile)) {
                File::delete($zipFile);
            }

            return back()->with('error', 'Error zipping files: ' . $e->getMessage());
        }
    }

    /**
     * Download directory recursively
     *
     * @param string $remotePath Remote directory path
     * @param string $localPath Local directory path
     * @param string $basePath Base path
     */
    protected function downloadDirectory($remotePath, $localPath, $basePath)
    {
        try {
            Log::debug('WebFTP downloadDirectory: Downloading directory', [
                'remote_path' => $remotePath,
                'local_path' => $localPath
            ]);
            
            $list = ftp_rawlist($this->connection, $remotePath);
            
            if (!$list) {
                Log::debug('WebFTP downloadDirectory: Empty or invalid directory', [
                    'remote_path' => $remotePath
                ]);
                return;
            }
            
            foreach ($list as $item) {
                $parsedItem = $this->parseListItem($item, $remotePath);
                
                if (!$parsedItem) {
                    continue;
                }
                
                $itemPath = $parsedItem['path'];
                $relativePath = str_replace($basePath . '/', '', $itemPath);
                $newLocalPath = $localPath . '/' . basename($itemPath);
                
                if ($parsedItem['is_dir']) {
                    // Create the directory locally
                    if (!File::exists($newLocalPath)) {
                        File::makeDirectory($newLocalPath, 0755, true);
                        Log::debug('WebFTP downloadDirectory: Created local directory', [
                            'local_path' => $newLocalPath
                        ]);
                    }
                    
                    // Download subdirectory
                    $this->downloadDirectory($itemPath, $newLocalPath, $basePath);
                } else {
                    // Download the file
                    ftp_get($this->connection, $newLocalPath, $itemPath, FTP_BINARY);
                    Log::debug('WebFTP downloadDirectory: Downloaded file', [
                        'remote_path' => $itemPath,
                        'local_path' => $newLocalPath
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Error downloading directory: ' . $e->getMessage(), [
                'path' => $remotePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Upload directory recursively
     *
     * @param string $localPath Local directory path
     * @param string $remotePath Remote directory path
     */
    protected function uploadDirectory($localPath, $remotePath)
    {
        try {
            Log::debug('WebFTP uploadDirectory: Uploading directory', [
                'local_path' => $localPath,
                'remote_path' => $remotePath
            ]);
            
            $files = File::allFiles($localPath);
            
            foreach ($files as $file) {
                $relativePath = substr($file->getPathname(), strlen($localPath) + 1);
                $remoteFilePath = $remotePath . '/' . $relativePath;
                
                // Create parent directories if needed
                $dirPath = dirname($remoteFilePath);
                $this->createRemoteDirectories($dirPath);
                
                // Upload file
                ftp_put($this->connection, $remoteFilePath, $file->getPathname(), FTP_BINARY);
                
                Log::debug('WebFTP uploadDirectory: Uploaded file', [
                    'local_path' => $file->getPathname(),
                    'remote_path' => $remoteFilePath
                ]);
            }
            
            Log::debug('WebFTP uploadDirectory: Completed directory upload', [
                'local_path' => $localPath,
                'remote_path' => $remotePath,
                'files_count' => count($files)
            ]);
        } catch (Exception $e) {
            Log::error('Error uploading directory: ' . $e->getMessage(), [
                'localPath' => $localPath,
                'remotePath' => $remotePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Unzip a zip file
     *
     * @param Request $request
     * @param string $username Hosting account username
     * @param string $path Directory path
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unzipFile(Request $request, $username, $path)
    {
        try {
            // Get user ID
            $userId = Auth::id();
            
            Log::info('WebFTP unzipFile: Start extracting zip', [
                'username' => $username,
                'path' => $path,
                'user_id' => $userId
            ]);
            
            // Check if user is authenticated
            if (!$userId) {
                return back()->with('error', 'Please login to use WebFTP.');
            }
            
            // Check if zip operations are allowed
            if (!$this->settings->allow_zip_operations) {
                return back()->with('error', 'Zip operations are disabled.');
            }
            
            // Validate request
            $request->validate([
                'zip_file' => 'required|string',
                'extract_to' => 'required|string'
            ]);

            // Get the hosting account
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return back()->with('error', 'Account must be active to use Web FTP.');
            }

            // Ensure paths start with /htdocs
            $zipPath = $this->ensureHtdocsPath($request->zip_file);
            $extractPath = $this->ensureHtdocsPath($request->extract_to);
            
            Log::info('WebFTP unzipFile: Paths prepared', [
                'zip_path' => $zipPath,
                'extract_path' => $extractPath
            ]);
            
            // Ensure temp directory exists
            $this->tempDir = $this->ensureTempDirectoryExists($userId);
            
            // Generate temp filenames
            $tempZip = $this->tempDir . '/' . Str::random(16) . '_' . basename($zipPath);
            $tempExtractDir = $this->tempDir . '/extract_' . Str::random(8);
            File::makeDirectory($tempExtractDir, 0755, true);
            
            // Connect to FTP
            $connection = $this->connect($account);

            if (!$connection['success']) {
                File::deleteDirectory($tempExtractDir);
                return back()->with('error', $connection['message']);
            }
            
            // Download the zip file
            if (!ftp_get($this->connection, $tempZip, $zipPath, FTP_BINARY)) {
                $this->disconnect();
                File::deleteDirectory($tempExtractDir);
                Log::error('WebFTP unzipFile: Failed to download zip file', [
                    'zip_path' => $zipPath,
                    'temp_zip' => $tempZip
                ]);
                return back()->with('error', 'Failed to download zip file.');
            }
            
            Log::info('WebFTP unzipFile: Downloaded zip file', [
                'temp_zip' => $tempZip,
                'size' => filesize($tempZip)
            ]);
            
            // Extract zip file
            $zip = new ZipArchive();
            if ($zip->open($tempZip) === true) {
                $zip->extractTo($tempExtractDir);
                $fileCount = $zip->numFiles;
                $zip->close();
                
                Log::info('WebFTP unzipFile: Extracted zip file', [
                    'temp_extract_dir' => $tempExtractDir,
                    'file_count' => $fileCount
                ]);
            } else {
                $this->disconnect();
                File::deleteDirectory($tempExtractDir);
                File::delete($tempZip);
                Log::error('WebFTP unzipFile: Failed to extract zip file');
                return back()->with('error', 'Failed to extract zip file.');
            }
            
            // Create extract directory on FTP if it doesn't exist
            if (!$this->directoryExists($extractPath)) {
                ftp_mkdir($this->connection, $extractPath);
                Log::info('WebFTP unzipFile: Created extract directory on FTP', [
                    'extract_path' => $extractPath
                ]);
            }
            
            // Upload extracted files
            $this->uploadDirectory($tempExtractDir, $extractPath);
            
            // Close connection
            $this->disconnect();
            
            // Clean up temp files
            File::deleteDirectory($tempExtractDir);
            File::delete($tempZip);
            
            return back()->with('success', 'File extracted successfully.');

        } catch (Exception $e) {
            Log::error('WebFTP Unzip Error: ' . $e->getMessage(), [
                'username' => $username,
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up temp files if they exist
            if (isset($tempExtractDir) && File::exists($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }
            
            if (isset($tempZip) && File::exists($tempZip)) {
                File::delete($tempZip);
            }

            return back()->with('error', 'Error extracting file: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to external file manager
     *
     * @param string $username Hosting account username
     * @param int $userId User ID
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToExternalFileManager($username, $userId)
    {
        try {
            Log::info('WebFTP redirectToExternalFileManager: Redirecting to external file manager', [
                'username' => $username,
                'user_id' => $userId
            ]);
            
            $account = HostingAccount::where('user_id', $userId)
                ->where('username', $username)
                ->firstOrFail();
                
            if ($account->status !== 'active') {
                return redirect()->route('hosting.view', $username)
                    ->with('error', 'Account must be active to use file manager.');
            }
            
            return redirect()->route('hosting.filemanager', $username)
                ->with('info', 'Using external file manager service.');
                
        } catch (Exception $e) {
            Log::error('Error redirecting to external file manager: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('hosting.view', $username)
                ->with('error', 'Error accessing file manager: ' . $e->getMessage());
        }
    }
    
    /**
 * Change file/directory permissions (chmod)
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function chmod(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        Log::info('WebFTP chmod: Start changing permissions', [
            'username' => $username,
            'user_id' => $userId
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Validate request
        $request->validate([
            'path' => 'required|string',
            'permissions' => 'required|integer|min:0|max:777',
            'recursive' => 'boolean'
        ]);

        // Convert permissions from octal (e.g. 755) to mode (e.g. 0755)
        $mode = octdec('0' . $request->permissions);
        $isRecursive = $request->recursive ?? false;

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account must be active to use Web FTP.'
            ]);
        }

        // Ensure path starts with /htdocs
        $path = $this->ensureHtdocsPath($request->path);
        
        Log::info('WebFTP chmod: Path and permissions prepared', [
            'path' => $path,
            'permissions' => $request->permissions,
            'mode' => $mode,
            'recursive' => $isRecursive
        ]);
        
        // Connect to FTP
        $connection = $this->connect($account);

        if (!$connection['success']) {
            return response()->json([
                'success' => false,
                'message' => $connection['message']
            ]);
        }
        
        // Change permissions
        if ($isRecursive) {
            // For recursive chmod, we need to determine if it's a directory
            $isDir = $this->isDirectory($path);
            
            if ($isDir) {
                $result = $this->chmodRecursive($path, $mode);
            } else {
                $result = ftp_chmod($this->connection, $mode, $path);
            }
        } else {
            // For non-recursive, simply chmod the path
            $result = ftp_chmod($this->connection, $mode, $path);
        }
        
        // Close connection
        $this->disconnect();
        
        if ($result === false) {
            Log::error('WebFTP chmod: Failed to change permissions', [
                'path' => $path,
                'permissions' => $request->permissions
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to change permissions.'
            ]);
        }
        
        Log::info('WebFTP chmod: Permissions changed successfully', [
            'path' => $path,
            'permissions' => $request->permissions
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Permissions changed successfully.',
            'permissions' => sprintf('%04o', $mode) // Convert back to readable octal
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Chmod Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error changing permissions: ' . $e->getMessage()
        ]);
    }
}

/**
 * Change permissions recursively for a directory
 *
 * @param string $path Directory path
 * @param int $mode Permissions mode
 * @return bool Success status
 */
protected function chmodRecursive($path, $mode)
{
    try {
        Log::debug('WebFTP chmodRecursive: Starting recursive chmod', [
            'path' => $path,
            'mode' => sprintf('%04o', $mode)
        ]);
        
        // First, chmod the parent directory
        $result = ftp_chmod($this->connection, $mode, $path);
        
        if ($result === false) {
            Log::error('WebFTP chmodRecursive: Failed to chmod parent directory', [
                'path' => $path
            ]);
            return false;
        }
        
        // Get directory contents
        $list = ftp_rawlist($this->connection, $path);
        
        if (!$list) {
            // Empty directory, we already changed its permissions
            return true;
        }
        
        // Process directory contents
        foreach ($list as $item) {
            $parsedItem = $this->parseListItem($item, $path);
            
            if (!$parsedItem) {
                continue;
            }
            
            $itemPath = $parsedItem['path'];
            
            if ($parsedItem['is_dir']) {
                // Recursive chmod for subdirectory
                $this->chmodRecursive($itemPath, $mode);
            } else {
                // Chmod file
                ftp_chmod($this->connection, $mode, $itemPath);
            }
        }
        
        return true;
    } catch (Exception $e) {
        Log::error('Error in chmodRecursive: ' . $e->getMessage(), [
            'path' => $path,
            'error' => $e->getMessage()
        ]);
        
        return false;
    }
}
/**
 * Copy files/directories to clipboard
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function copyToClipboard(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        Log::info('WebFTP copyToClipboard: Copying items to clipboard', [
            'username' => $username,
            'user_id' => $userId
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Validate request
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|string',
            'current_path' => 'required|string'
        ]);

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account must be active to use Web FTP.'
            ]);
        }
        
        // Prepare items for clipboard
        $clipboardItems = [];
        $currentPath = $this->ensureHtdocsPath($request->current_path);
        
        foreach ($request->items as $item) {
            $path = $this->ensureHtdocsPath($item);
            
            // Connect to FTP to check if item is a directory
            if (!$this->isConnected) {
                $connection = $this->connect($account);
                if (!$connection['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $connection['message']
                    ]);
                }
            }
            
            $isDir = $this->isDirectory($path);
            
            $clipboardItems[] = [
                'path' => $path,
                'is_dir' => $isDir,
                'name' => basename($path)
            ];
        }
        
        // Disconnect if we had to connect
        if ($this->isConnected) {
            $this->disconnect();
        }
        
        // Store in session
        session(['webftp_clipboard' => $clipboardItems]);
        session(['webftp_clipboard_action' => 'copy']);
        session(['webftp_clipboard_source_path' => $currentPath]);
        
        Log::info('WebFTP copyToClipboard: Items copied to clipboard', [
            'items_count' => count($clipboardItems)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => count($clipboardItems) . ' item(s) copied to clipboard.',
            'items' => $clipboardItems
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Copy Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error copying items: ' . $e->getMessage()
        ]);
    }
}

/**
 * Cut files/directories to clipboard (for moving)
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function cutToClipboard(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        Log::info('WebFTP cutToClipboard: Cutting items to clipboard', [
            'username' => $username,
            'user_id' => $userId
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Validate request
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|string',
            'current_path' => 'required|string'
        ]);

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account must be active to use Web FTP.'
            ]);
        }
        
        // Prepare items for clipboard
        $clipboardItems = [];
        $currentPath = $this->ensureHtdocsPath($request->current_path);
        
        foreach ($request->items as $item) {
            $path = $this->ensureHtdocsPath($item);
            
            // Connect to FTP to check if item is a directory
            if (!$this->isConnected) {
                $connection = $this->connect($account);
                if (!$connection['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $connection['message']
                    ]);
                }
            }
            
            $isDir = $this->isDirectory($path);
            
            $clipboardItems[] = [
                'path' => $path,
                'is_dir' => $isDir,
                'name' => basename($path)
            ];
        }
        
        // Disconnect if we had to connect
        if ($this->isConnected) {
            $this->disconnect();
        }
        
        // Store in session
        session(['webftp_clipboard' => $clipboardItems]);
        session(['webftp_clipboard_action' => 'cut']);
        session(['webftp_clipboard_source_path' => $currentPath]);
        
        Log::info('WebFTP cutToClipboard: Items cut to clipboard', [
            'items_count' => count($clipboardItems)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => count($clipboardItems) . ' item(s) cut to clipboard.',
            'items' => $clipboardItems
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Cut Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error cutting items: ' . $e->getMessage()
        ]);
    }
}

/**
 * Paste files/directories from clipboard
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function paste(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        Log::info('WebFTP paste: Start pasting items from clipboard', [
            'username' => $username,
            'user_id' => $userId
        ]);
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Validate request
        $request->validate([
            'destination_path' => 'required|string'
        ]);

        // Get the hosting account
        $account = HostingAccount::where('user_id', $userId)
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account must be active to use Web FTP.'
            ]);
        }
        
        // Get clipboard items from session
        $clipboardItems = session('webftp_clipboard', []);
        $clipboardAction = session('webftp_clipboard_action', 'copy');
        $sourcePath = session('webftp_clipboard_source_path', '');
        
        if (empty($clipboardItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Clipboard is empty. Please copy or cut items first.'
            ]);
        }
        
        // Prepare destination path
        $destinationPath = $this->ensureHtdocsPath($request->destination_path);
        
        Log::info('WebFTP paste: Paths prepared', [
            'destination_path' => $destinationPath,
            'source_path' => $sourcePath,
            'action' => $clipboardAction,
            'items_count' => count($clipboardItems)
        ]);
        
        // Connect to FTP
        $connection = $this->connect($account);

        if (!$connection['success']) {
            return response()->json([
                'success' => false,
                'message' => $connection['message']
            ]);
        }
        
        // Ensure the destination directory exists
        if (!$this->directoryExists($destinationPath)) {
            $this->createRemoteDirectories($destinationPath);
        }
        
        // Ensure temp directory exists for file transfers
        $this->tempDir = $this->ensureTempDirectoryExists($userId);
        
        // Process each item
        $processedItems = [];
        
        foreach ($clipboardItems as $item) {
            $sourceItemPath = $item['path'];
            $isDir = $item['is_dir'];
            $name = $item['name'];
            
            // Check if this is a conflict (copying to same directory with same name)
            if ($sourceItemPath === $destinationPath . '/' . $name) {
                $error = 'Cannot copy item to itself: ' . $name;
                Log::warning('WebFTP paste: ' . $error);
                
                $processedItems[] = [
                    'name' => $name,
                    'success' => false,
                    'message' => $error
                ];
                
                continue;
            }
            
            // Prepare destination item path
            $destItemPath = $destinationPath . '/' . $name;
            
            // Check if the destination already exists
            $destExists = false;
            try {
                if ($isDir) {
                    $destExists = $this->directoryExists($destItemPath);
                } else {
                    // For files, try to get file size (will fail if not exists)
                    $size = @ftp_size($this->connection, $destItemPath);
                    $destExists = ($size !== -1);
                }
            } catch (Exception $e) {
                // If exception, assume it doesn't exist
                $destExists = false;
            }
            
            Log::info('WebFTP paste: Processing item', [
                'name' => $name,
                'source' => $sourceItemPath,
                'destination' => $destItemPath,
                'is_dir' => $isDir,
                'dest_exists' => $destExists
            ]);
            
            // If destination exists, create a numbered copy
            if ($destExists) {
                $baseName = pathinfo($name, PATHINFO_FILENAME);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $suffix = 1;
                
                do {
                    $newName = $baseName . ' (' . $suffix . ')';
                    if (!empty($extension)) {
                        $newName .= '.' . $extension;
                    }
                    
                    $destItemPath = $destinationPath . '/' . $newName;
                    
                    if ($isDir) {
                        $destExists = $this->directoryExists($destItemPath);
                    } else {
                        $size = @ftp_size($this->connection, $destItemPath);
                        $destExists = ($size !== -1);
                    }
                    
                    $suffix++;
                } while ($destExists);
                
                Log::info('WebFTP paste: Renamed to avoid conflict', [
                    'original_name' => $name,
                    'new_name' => basename($destItemPath)
                ]);
            }
            
            // Process based on item type
            $success = false;
            $message = '';
            
            if ($isDir) {
                if ($clipboardAction === 'copy') {
                    // For directories, we need to copy recursively
                    // Create a temporary local directory for copying
                    $tempLocalDir = $this->tempDir . '/copy_' . Str::random(8);
                    File::makeDirectory($tempLocalDir, 0755, true);
                    
                    try {
                        // Download directory (and contents) to temp location
                        $this->downloadDirectory($sourceItemPath, $tempLocalDir, $sourceItemPath);
                        
                        // Create destination directory
                        if (!$this->directoryExists($destItemPath)) {
                            ftp_mkdir($this->connection, $destItemPath);
                        }
                        
                        // Upload directory contents to new location
                        $this->uploadDirectory($tempLocalDir, $destItemPath);
                        
                        $success = true;
                        $message = 'Directory copied successfully.';
                        
                        // Clean up temp directory
                        File::deleteDirectory($tempLocalDir);
                    } catch (Exception $e) {
                        Log::error('WebFTP paste: Error copying directory', [
                            'error' => $e->getMessage(),
                            'source' => $sourceItemPath,
                            'destination' => $destItemPath
                        ]);
                        
                        if (File::exists($tempLocalDir)) {
                            File::deleteDirectory($tempLocalDir);
                        }
                        
                        $success = false;
                        $message = 'Error copying directory: ' . $e->getMessage();
                    }
                } else {
                    // For move (cut & paste), we can use ftp_rename
                    try {
                        $success = ftp_rename($this->connection, $sourceItemPath, $destItemPath);
                        
                        if ($success) {
                            $message = 'Directory moved successfully.';
                        } else {
                            $message = 'Failed to move directory.';
                        }
                    } catch (Exception $e) {
                        Log::error('WebFTP paste: Error moving directory', [
                            'error' => $e->getMessage(),
                            'source' => $sourceItemPath,
                            'destination' => $destItemPath
                        ]);
                        
                        $success = false;
                        $message = 'Error moving directory: ' . $e->getMessage();
                    }
                }
            } else {
                // For files
                if ($clipboardAction === 'copy') {
                    // Download the file to a temporary location
                    $tempFile = $this->tempDir . '/' . Str::random(16) . '_' . $name;
                    
                    try {
                        // Download file
                        if (ftp_get($this->connection, $tempFile, $sourceItemPath, FTP_BINARY)) {
                            // Upload file to new location
                            $success = ftp_put($this->connection, $destItemPath, $tempFile, FTP_BINARY);
                            
                            if ($success) {
                                $message = 'File copied successfully.';
                            } else {
                                $message = 'Failed to upload file to new location.';
                            }
                        } else {
                            $success = false;
                            $message = 'Failed to download source file.';
                        }
                        
                        // Clean up temp file
                        if (File::exists($tempFile)) {
                            File::delete($tempFile);
                        }
                    } catch (Exception $e) {
                        Log::error('WebFTP paste: Error copying file', [
                            'error' => $e->getMessage(),
                            'source' => $sourceItemPath,
                            'destination' => $destItemPath
                        ]);
                        
                        if (File::exists($tempFile)) {
                            File::delete($tempFile);
                        }
                        
                        $success = false;
                        $message = 'Error copying file: ' . $e->getMessage();
                    }
                } else {
                    // For move (cut & paste), we can use ftp_rename
                    try {
                        $success = ftp_rename($this->connection, $sourceItemPath, $destItemPath);
                        
                        if ($success) {
                            $message = 'File moved successfully.';
                        } else {
                            $message = 'Failed to move file.';
                        }
                    } catch (Exception $e) {
                        Log::error('WebFTP paste: Error moving file', [
                            'error' => $e->getMessage(),
                            'source' => $sourceItemPath,
                            'destination' => $destItemPath
                        ]);
                        
                        $success = false;
                        $message = 'Error moving file: ' . $e->getMessage();
                    }
                }
            }
            
            $processedItems[] = [
                'name' => $name,
                'success' => $success,
                'message' => $message,
                'new_path' => $destItemPath,
                'new_name' => basename($destItemPath)
            ];
            
            Log::info('WebFTP paste: Item processed', [
                'name' => $name,
                'success' => $success,
                'message' => $message
            ]);
        }
        
        // Close connection
        $this->disconnect();
        
        // Clear clipboard after paste
        if ($clipboardAction === 'cut') {
            session()->forget(['webftp_clipboard', 'webftp_clipboard_action', 'webftp_clipboard_source_path']);
        }
        
        // Count successes
        $successCount = collect($processedItems)->where('success', true)->count();
        
        return response()->json([
            'success' => true,
            'message' => $successCount . ' of ' . count($clipboardItems) . ' item(s) pasted successfully.',
            'processed_items' => $processedItems,
            'action' => $clipboardAction
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Paste Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error pasting items: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get clipboard status
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function getClipboardStatus(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Get clipboard items from session
        $clipboardItems = session('webftp_clipboard', []);
        $clipboardAction = session('webftp_clipboard_action', '');
        $sourcePath = session('webftp_clipboard_source_path', '');
        
        return response()->json([
            'success' => true,
            'has_items' => !empty($clipboardItems),
            'items_count' => count($clipboardItems),
            'action' => $clipboardAction,
            'source_path' => $sourcePath
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Clipboard Status Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error getting clipboard status: ' . $e->getMessage()
        ]);
    }
}

/**
 * Clear clipboard
 *
 * @param Request $request
 * @param string $username Hosting account username
 * @return \Illuminate\Http\JsonResponse
 */
public function clearClipboard(Request $request, $username)
{
    try {
        // Get user ID
        $userId = Auth::id();
        
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to use WebFTP.'
            ]);
        }
        
        // Clear clipboard session variables
        session()->forget(['webftp_clipboard', 'webftp_clipboard_action', 'webftp_clipboard_source_path']);
        
        return response()->json([
            'success' => true,
            'message' => 'Clipboard cleared successfully.'
        ]);

    } catch (Exception $e) {
        Log::error('WebFTP Clear Clipboard Error: ' . $e->getMessage(), [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error clearing clipboard: ' . $e->getMessage()
        ]);
    }
}

/**
 * Determine if the path is outside htdocs (in the root directory)
 *
 * @param string $path Path to check
 * @return bool True if in root directory
 */
protected function isRootPath($path)
{
    // Normalize path first
    $path = $this->sanitizePath($path);
    
    // Check if the path is / or doesn't start with /htdocs
    return $path === '/' || (strpos($path, '/htdocs') !== 0 && strpos($path, '/www') !== 0);
}

/**
 * Get all parent directories for a path, including root level
 *
 * @param string $path Path to process
 * @return array Array of parent directories
 */
protected function getAllParentDirectories($path)
{
    $path = $this->sanitizePath($path);
    $parents = ['/'];
    
    if ($path === '/') {
        return $parents;
    }
    
    $parts = explode('/', trim($path, '/'));
    $currentPath = '';
    
    foreach ($parts as $part) {
        $currentPath .= '/' . $part;
        $parents[] = $this->sanitizePath($currentPath);
    }
    
    return $parents;
}

/**
 * Handle path for FTP operations with root access
 *
 * @param string $path Path to handle
 * @return string Processed path
 */
protected function handlePath($path)
{
    // Sanitize path first
    $path = $this->sanitizePath($path);
    
    // Determine if we should process this path as a root path
    $allowRootAccess = true; // You might want to make this configurable
    
    // If root access is allowed, return the path as is
    if ($allowRootAccess && $this->isRootPath($path)) {
        Log::debug('WebFTP handlePath: Root access path', [
            'input_path' => $path,
            'is_root' => true
        ]);
        return $path;
    }
    
    // Otherwise, ensure the path starts with /htdocs
    return $this->ensureHtdocsPath($path);
}

/**
 * Override the existing ensureHtdocsPath method to allow root access
 *
 * @param string $path Path to check
 * @return string Path with /htdocs prefix if needed
 */
protected function ensureHtdocsPath($path)
{
    // Log the incoming path
    Log::debug('WebFTP ensureHtdocsPath: Processing path', [
        'input_path' => $path
    ]);
    
    // Allow root access if the path is explicitly '/'
    if ($path === '/') {
        Log::debug('WebFTP ensureHtdocsPath: Root path detected, not modifying');
        return '/';
    }
    
    // Sanitize path first
    $path = $this->sanitizePath($path);
    
    Log::debug('WebFTP ensureHtdocsPath: After sanitizing', [
        'sanitized_path' => $path
    ]);
    
    // Check if path already starts with /htdocs
    if (strpos($path, '/htdocs') !== 0) {
        // If it's just a subpath, add /htdocs prefix
        $result = '/htdocs' . ($path !== '/' ? $path : '');
        Log::debug('WebFTP ensureHtdocsPath: Added htdocs prefix', [
            'result_path' => $result
        ]);
        return $result;
    }
    
    Log::debug('WebFTP ensureHtdocsPath: Path already has htdocs prefix', [
        'path' => $path
    ]);
    return $path;
}

/**
 * Get path parts for breadcrumbs, including root level
 *
 * @param string $path Path to process
 * @return array Array of path parts for breadcrumbs
 */
protected function getPathParts($path)
{
    Log::debug('WebFTP getPathParts: Processing path', [
        'input_path' => $path
    ]);
    
    // Handle the root directory
    if ($path === '/') {
        Log::debug('WebFTP getPathParts: Root path detected');
        return [['name' => 'Root', 'path' => '/']];
    }
    
    $parts = explode('/', trim($path, '/'));
    $result = [['name' => 'Root', 'path' => '/']];
    $currentPath = '';
    
    foreach ($parts as $part) {
        $currentPath .= '/' . $part;
        $result[] = [
            'name' => $part,
            'path' => $currentPath
        ];
        
        Log::debug('WebFTP getPathParts: Added part', [
            'part' => $part,
            'current_path' => $currentPath
        ]);
    }
    
    Log::debug('WebFTP getPathParts: Result', [
        'parts_count' => count($result),
        'parts' => $result
    ]);
    
    return $result;
}
}