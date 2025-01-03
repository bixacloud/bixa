<!-- Main Content -->
<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">

           <!-- Page Title -->
           <div class="row">
               <div class="col-12">
                   <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                       <h4 class="mb-sm-0 font-size-18"><?= $this->base->text('account_details', 'heading') ?></h4>
                   </div>
               </div>
           </div>

        <!-- Flash message -->
<?php 
if($this->session->flashdata('msg')): 
    $msg = json_decode($this->session->flashdata('msg'), true);
    $this->session->unset_userdata('msg');
?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?= $msg[0] ? "success" : "error" ?>',
            text: '<?= addslashes($msg[1]) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast <?= $msg[0] ? "success-toast" : "error-toast" ?>'
            }
        });
    });
    </script>
<?php endif; ?>

<!-- Account status notifications -->
<?php if($data['account_status'] === 'pending'): ?>
    <?php $time = $data['account_time'] + 3600; ?>
    <?php if($time > time()): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                text: '<?= addslashes($this->base->text('account_note', 'paragraph')) ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                customClass: {
                    popup: 'custom-toast info-toast'
                }
            });
        });
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php if($data['account_status'] === 'pending'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            text: '<?= addslashes($this->base->text('account_pending', 'paragraph')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast info-toast'
            }
        });
    });
    </script>
<?php elseif($data['account_status'] === 'reactivating'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            text: '<?= addslashes($this->base->text('account_reactivating', 'paragraph')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast warning-toast'
            }
        });
    });
    </script>
<?php elseif($data['account_status'] === 'deactivating'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning', 
            text: '<?= addslashes($this->base->text('account_deactivating', 'paragraph')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast warning-toast'
            }
        });
    });
    </script>
<?php elseif($data['account_status'] === 'suspended'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            text: '<?= addslashes($this->base->text('account_suspended', 'paragraph')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast error-toast'
            }
        });
    });
    </script>
<?php elseif($data['account_status'] === 'deactivated'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            text: '<?= addslashes($this->base->text('account_deactivated', 'paragraph')) ?>',
            toast: true, 
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-toast success-toast'
            }
        });
    });
    </script>
<?php endif; ?>

<style>
/* Base toast style */
.custom-toast {
    font-size: 14px !important;
    padding: 12px 20px !important;
    max-width: 500px !important;
    width: auto !important;
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
    border-left: 4px solid !important;
}

/* Success toast */
.success-toast {
    background-color: #d4edda !important;
    color: #155724 !important;
    border-left-color: #28a745 !important;
}

/* Error toast */
.error-toast {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border-left-color: #dc3545 !important;
}

/* Warning toast */
.warning-toast {
    background-color: #fff3cd !important;
    color: #856404 !important;
    border-left-color: #ffc107 !important;
}

/* Info toast */
.info-toast {
    background-color: #cce5ff !important;
    color: #004085 !important;
    border-left-color: #17a2b8 !important;
}

.custom-toast .swal2-html-container {
    margin: 5px 0 !important;
    text-align: left !important;
}
</style>

          <!-- Stats Cards -->
<div class="row">
    <!-- Status Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-3">
                            <i data-feather="server" class="font-size-24"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-2"><?= $this->base->text('status', 'table') ?></p>
                        <h5 class="mb-0">
                            <?php if($data['account_status'] === 'active'): ?>
                                <span class="badge rounded-pill bg-success"><?= $this->base->text($data['account_status'], 'table') ?></span>
                            <?php elseif($data['account_status'] === 'suspended'): ?>
                                <span class="badge rounded-pill bg-danger"><?= $this->base->text('suspended', 'table') ?></span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-warning"><?= ucfirst($data['account_status']) ?></span>
                            <?php endif; ?>
                        </h5>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar <?= $data['account_status'] === 'active' ? 'bg-success' : 'bg-danger' ?>" style="width: 100%"></div>
                        </div>
                        <small class="text-muted">&nbsp;</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder Stats Cards -->
    <?php foreach(['disk', 'bandwidth', 'inodes'] as $stat): ?>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card" data-stat="<?= $stat ?>">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Control Panel -->
                    <div class="col-md-6 col-lg-3">
                        <a href="<?= $data['account_status'] === 'active' ? base_url() . 'account/view/' . $id . '/cpanel' : '#' ?>" 
                           class="btn btn-primary w-100 waves-effect waves-light <?= $data['account_status'] !== 'active' ? 'disabled' : '' ?>"
                           target="_blank">
                            <i data-feather="monitor" class="font-size-16 align-middle me-2"></i>
                            <?= $this->base->text('control_panel', 'button') ?>
                        </a>
                    </div>

                    <!-- File Manager -->
                    <div class="col-md-6 col-lg-3">
                        <a href="<?= $data['account_status'] === 'active' ? base_url() . 'account/view/' . $id . '/filemanager' : '#' ?>"
                           class="btn btn-info w-100 waves-effect waves-light <?= $data['account_status'] !== 'active' ? 'disabled' : '' ?>" 
                           target="_blank">
                            <i data-feather="folder" class="font-size-16 align-middle me-2"></i>
                            <?= $this->base->text('file_manager', 'button') ?>
                        </a>
                    </div>                   
                    <!-- Softaculous -->
                    <div class="col-md-6 col-lg-3">
                        <a href="<?= $data['account_status'] === 'active' ? base_url() . 'account/view/' . $id . '/softaculous' : '#' ?>" 
                           class="btn btn-secondary w-100 waves-effect waves-light <?= $data['account_status'] !== 'active' ? 'disabled' : '' ?>"
                           target="_blank">
                            <i data-feather="box" class="font-size-16 align-middle me-2"></i>
                            <?= $this->base->text('softaculous', 'button') ?>
                        </a>
                    </div>
                  <!-- Settings -->
                    <div class="col-md-6 col-lg-3">
                                    <?php if ($data['account_status'] === 'deactivated'): ?>
                                        <a href="<?= base_url() ?>account/view/<?= $id ?>?reactivate=true" 
                                           class="btn btn-success w-100 waves-effect waves-light">
                                            <i data-feather="refresh-cw" class="font-size-16 align-middle me-2"></i>
                                            Reactivate
                                        </a>
                                    <?php elseif ($data['account_status'] === 'suspended'): ?>
                                        <a href="<?= base_url() ?>ticket/create" 
                                           class="btn btn-warning w-100 waves-effect waves-light">
                                            <i data-feather="message-square" class="font-size-16 align-middle me-2"></i>
                                            Open Ticket
                                        </a>
                                    <?php else: ?>
                         <a href="<?= $data['account_status'] === 'active' ? base_url() . 'account/settings/' . $id : '#' ?>" 
                           class="btn btn-dark w-100 waves-effect waves-light <?= $data['account_status'] !== 'active' ? 'disabled' : '' ?>">
                            <i data-feather="settings" class="font-size-16 align-middle me-2"></i>
                            <?= $this->base->text('settings', 'button') ?>
                        </a>
                                    <?php endif; ?>
                                </div>
                </div>
            </div>
        </div>
    </div>
</div>

           <!-- Account Info & Connection Info -->
           <div class="row">
               <!-- Account Info -->
               <div class="col-lg-8">
                   <div class="card">
                       <div class="card-header">
                           <h4 class="card-title mb-0"><?= $this->base->text('account_details', 'heading') ?></h4>
                       </div>
                       <div class="card-body">
                           <div class="table-responsive">
                               <table class="table table-nowrap mb-0">
                                   <tbody>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="user" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('username', 'table') ?>
                                               </div>
                                           </th>
                                           <td><?= $data['account_status'] === 'active' ? $data['account_username'] : $this->base->text('loading', 'label') ?></td>
                                       </tr>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="key" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('password', 'table') ?>
                                               </div>
                                           </th>
                                           <td>
                                               <div class="d-flex align-items-center">
                                                   <span id="password-hidden">••••••••</span>
                                                   <span id="password-shown" class="d-none">
                                                       <?= $data['account_status'] === 'active' ? $data['account_password'] : '••••••••' ?>
                                                   </span>
                                                   <a href="javascript:void(0);" onclick="togglePassword()" class="text-muted ms-2">
                                                       <i class="fas fa-eye"></i>
                                                   </a>
                                               </div>
                                           </td>
                                       </tr>
                                       <tr>
    <th scope="row">
        <div class="d-flex align-items-center">
            <i data-feather="server" class="font-size-20 text-primary me-2"></i>
            <?= $this->base->text('server_ip', 'table') ?>
        </div>
    </th>
    <td>
        <?php if($data['account_status'] === 'active'): ?>
            <?php if(isset($server_ip) && $server_ip): ?>
                <?= $server_ip ?>
            <?php else: ?>
                <span class="text-muted">
                    IP not found for domain: <?= $data['account_domain'] ?>
                </span>
            <?php endif; ?>
        <?php else: ?>
            <?= $this->base->text('loading', 'label') ?>
        <?php endif; ?>
    </td>
</tr>
                                       <tr>
    <th scope="row">
        <div class="d-flex align-items-center">
            <i data-feather="globe" class="font-size-20 text-primary me-2"></i>
            <?= $this->base->text('domain', 'table') ?>
        </div>
    </th>
    <td>
        <?php if($data['account_status'] === 'active'): ?>
            <?php $domains = $this->account->get_domains($data['account_username'], $data['account_password'], $data['account_domain']) ?>
            <?php if(count($domains) > 0): ?>
                <?php foreach($domains as $domain): ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span><?= $domain['domain'] ?></span>
                        <a href="<?= base_url('account/view/'.$id.'/builder?domain='.$domain['domain']) ?>" 
                           class="btn btn-sm btn-primary waves-effect waves-light"
                           target="_blank">
                             <i class="fas fa-paint-brush me-2"></i>
                            SitePro
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?= $this->base->text('domain_list', 'paragraph') ?>
            <?php endif; ?>
        <?php else: ?>
            <?= $this->base->text('domain_list', 'paragraph') ?>
        <?php endif; ?>
    </td>
</tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
               </div>

               <!-- Connection Info -->
               <div class="col-lg-4">
                   <div class="card">
                       <div class="card-header">
                           <h4 class="card-title mb-0"><?= $this->base->text('connection_details', 'heading') ?></h4>
                       </div>
                       <div class="card-body">
                           <div class="table-responsive">
                               <table class="table table-nowrap mb-0">
                                   <tbody>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="database" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('mysql', 'table') ?>
                                               </div>
                                           </th>
                                           <td><?= $data['account_status'] === 'active' ? str_replace('cpanel', $data['account_sql'], $this->mofh->get_cpanel()) : $this->base->text('loading', 'label') ?></td>
                                       </tr>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="git-commit" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('port', 'table') ?>
                                               </div>
                                           </th>
                                           <td>3306</td>
                                       </tr>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="server" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('ftp', 'table') ?>
                                               </div>
                                           </th>
                                           <td>ftpupload.net</td>
                                       </tr>
                                       <tr>
                                           <th scope="row">
                                               <div class="d-flex align-items-center">
                                                   <i data-feather="git-commit" class="font-size-20 text-primary me-2"></i>
                                                   <?= $this->base->text('port', 'table') ?>
                                               </div>
                                           </th>
                                           <td>21</td>
                                       </tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
               </div>
           </div>

       </div>
   </div>
</div>

<script>
function loadAccountStats() {
    const accountId = '<?= $data['account_username'] ?>';
    console.log('Loading stats for:', accountId);

    fetch(`<?= base_url('u/get_account_stats/') ?>${accountId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    updateStatsCards(data.data);
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            } catch (e) {
                console.error('JSON Parse error:', e);
                throw new Error('Invalid server response');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStatsError(error.message);
        });
}

function updateStatsCards(stats) {
    // Update Disk Card
    const diskCard = document.querySelector('[data-stat="disk"]');
    diskCard.innerHTML = `
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar-sm">
                    <span class="avatar-title bg-warning-subtle text-warning rounded-3">
                        <i data-feather="database" class="font-size-24"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted mb-2"><?= $this->base->text('disk', 'table') ?></p>
                    <h5 class="mb-0">${stats.disk.used} ${stats.disk.unit} / ${stats.disk.total} ${stats.disk.unit}</h5>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar ${stats.disk.percent > 90 ? 'bg-danger' : 'bg-success'}" 
                             role="progressbar" 
                             style="width: ${stats.disk.percent}%" 
                             aria-valuenow="${stats.disk.percent}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">${stats.disk.percent}% used</small>
                </div>
            </div>
        </div>
    `;

    // Update Bandwidth Card
    const bandwidthCard = document.querySelector('[data-stat="bandwidth"]');
    bandwidthCard.innerHTML = `
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar-sm">
                    <span class="avatar-title bg-success-subtle text-success rounded-3">
                        <i data-feather="bar-chart-2" class="font-size-24"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted mb-2"><?= $this->base->text('bandwidth', 'table') ?></p>
                    ${stats.bandwidth.total === 'Unlimited' 
                        ? `<h5 class="mb-0">${stats.bandwidth.used} ${stats.bandwidth.unit} <small class="text-muted">/∞</small></h5>
                           <div class="mt-2">
                               <small class="text-muted">Unlimited bandwidth</small>
                           </div>`
                        : `<h5 class="mb-0">${stats.bandwidth.used} ${stats.bandwidth.unit} / ${stats.bandwidth.total} ${stats.bandwidth.unit}</h5>
                           <div class="progress mt-2" style="height: 5px;">
                               <div class="progress-bar ${stats.bandwidth.percent > 90 ? 'bg-danger' : 'bg-success'}" 
                                    role="progressbar" 
                                    style="width: ${stats.bandwidth.percent}%" 
                                    aria-valuenow="${stats.bandwidth.percent}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                               </div>
                           </div>
                           <small class="text-muted">${stats.bandwidth.percent}% used</small>`
                    }
                </div>
            </div>
        </div>
    `;

    // Update Inodes Card
    const inodesCard = document.querySelector('[data-stat="inodes"]');
    inodesCard.innerHTML = `
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar-sm">
                    <span class="avatar-title bg-danger-subtle text-danger rounded-3">
                        <i data-feather="hard-drive" class="font-size-24"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted mb-2"><?= $this->base->text('inodes', 'table') ?></p>
                    <h5 class="mb-0">${stats.inodes.used.toLocaleString()} / ${stats.inodes.total.toLocaleString()}</h5>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar ${stats.inodes.percent > 90 ? 'bg-danger' : 'bg-success'}" 
                             role="progressbar" 
                             style="width: ${stats.inodes.percent}%" 
                             aria-valuenow="${stats.inodes.percent}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">${stats.inodes.percent}% used</small>
                </div>
            </div>
        </div>
    `;

    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function showStatsError(message) {
    document.querySelectorAll('[data-stat]').forEach(card => {
        card.innerHTML = `
            <div class="card-body">
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-circle mb-2" style="font-size: 24px;"></i>
                    <p class="mb-0">${message}</p>
                    <button onclick="retryLoadStats()" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-sync-alt me-1"></i> Retry
                    </button>
                </div>
            </div>
        `;
    });
}

function retryLoadStats() {
    document.querySelectorAll('[data-stat]').forEach(card => {
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `;
    });

    setTimeout(loadAccountStats, 1000);
}

// Initialize if account is active
if ('<?= $data['account_status'] ?>' === 'active') {
    loadAccountStats();
}
function togglePassword() {
    const hidden = document.getElementById('password-hidden');
    const shown = document.getElementById('password-shown');
    
    if(hidden.classList.contains('d-none')) {
        hidden.classList.remove('d-none');
        shown.classList.add('d-none');
    } else {
        hidden.classList.add('d-none');
        shown.classList.remove('d-none');
    }
}
// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>