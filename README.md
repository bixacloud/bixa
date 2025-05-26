<div align="center">
    <img src="https://bixa.app/docs/logo.png" width="250px">
</div>

# Bixa - Hosting Management Platform

> **Note:**  
> **Development is active again!**
> A bugfix release is coming to address currently known issues.
> Pull requests are welcome and will still be accepted. If you want to see a feature, feel free to contribute it.

## 👀 What is Bixa?
Bixa is a comprehensive hosting account and support management system designed primarily for MOFH (MyOwnFreeHost). Built with Laravel 11, it provides a robust platform for managing hosting accounts, support tickets, SSL certificates, and more.

[![License](https://img.shields.io/badge/License-GPL_2.0-orange)](LICENSE)
[![Version](https://img.shields.io/badge/Version-v2.0.1-informational)](https://github.com/bixacloud/bixa/releases/latest)
![Build](https://img.shields.io/badge/Build-Passed-brightgreen)
![Framework](https://img.shields.io/badge/Framework-Laravel_11-red)
![Interface](https://img.shields.io/badge/Interface-Tabler-lightgreen)
![Development](https://img.shields.io/badge/Development-Active-brightgreen)

### 🎮 User Features
- **Dashboard**: Central hub with account overview and quick service access
- **Hosting Management**: Create and manage up to 3 hosting accounts
- **SSL Certificates**: Secure websites with free SSL certificates
- **Support Tickets**: Get technical assistance through the ticket system
- **Knowledge Base**: Access helpful articles and documentation
- **Profile Management**: Update personal information and security settings
- **Two-Factor Authentication**: Enhanced account security
- **Notifications**: Stay informed about account activities
- **Web FTP**: Manage website files directly through the browser
- **Tools**: Case converter, code beautifier, base64 encoder/decoder, and more
- **WHOIS Lookup**: Check domain registration information

### 👑 Admin Features
- **Admin Dashboard**: System performance monitoring and statistics
- **User Management**: Create, edit, and manage all user accounts
- **Hosting Management**: Configure MOFH API and server settings
- **Ticket System**: Handle support tickets and monitor staff performance
- **Knowledge Base Management**: Create and organize articles for users
- **Notification System**: Communicate with users through announcements
- **System Settings**: Configure platform behavior and appearance
- **Advertising Management**: Control on-site advertisements
- **Domain Management**: Manage allowed domain extensions
- **Email Templates**: Customize system-generated emails
- **Integrations**
	- MOFH (MyOwnFreeHost)
	- Google reCAPTCHA 
	- hCaptcha
	- IconCaptcha
 	- PageSpeed Insights
  	- Imgur API (Ticket Image Upload)
 	- Separate WebFTP  
	- Cloudflare DNS
	- ACMEv2 (Let's Encrypt)
	- Site.Pro
	- SMTP
- **Tools**
	- Case Converter
	- Code Beautifier
 	- Code Minifier
	- Colour Tools
	- Base64 Encoder
 	- Base64 Decoder
	- SQL Formatter
	- CDN Library Search
	- Website Speed Test
	- CSS Grid Generator
	- Froala License Generator
	- WHOIS Domain
	- WHOIS Domain (Check Multiple Domains)
- **0Auth Logins**
	- Facebook Login
 	- Google Login
- **Data Migration Tool** (BIXA Old Version & XERA Support)
- **Auto SSL For All Subdomains**
- **Authentication Logs**
- **Update Manager**
- **Multi-lingual**

## 🚀 Getting Started

### 🚅 Requirements
Your server needs to meet the following minimum requirements:
- PHP v8.1 or above
- MySQL v5.7 or above
- Laravel 11 compatible server
- A valid, trusted SSL certificate

### 💾 Installation 
For detailed installation instructions, please refer to our comprehensive documentation at [bixa.app/docs/installation](https://bixa.app/docs/install).

In brief:
1. Download the latest release from our [GitHub repository](https://github.com/bixacloud/bixa/releases/latest).
2. Upload the downloaded file to the server and extract it.
3. Go to ```https://{your.domain}/install``` and click on the 'Start Installation' button.
4.  Set your website's ```Website URL``` and hit the 'Next' button.
5.  Edit the database credentials and click on the 'Next' button (this will automatically import tables and records to the database).
6.  Installation completed and shows admin details (**Remember**: After completing the installation, please delete the "Install" folder from your public_html/public directory for security reasons.).

### 📧 SMTP Services
Here are some recommended SMTP services with free tiers that work well with Bixa:

- [Mailtrap](https://mailtrap.io/): 500 emails/month free (testing), 1,000 emails/month free (production)
- [Mailjet](https://mailjet.com/): 6,000 emails/month free
- [SendGrid](https://sendgrid.com/free/): 1000 emails/day free

## 📚 Documentation

For comprehensive documentation covering all aspects of using and administering Bixa, please visit our official documentation at [bixa.app/docs](https://bixa.app/docs).

The documentation includes:
- [User Guide](https://bixa.app/docs/user)
- [Admin Guide](https://bixa.app/docs/admin)
- [API Documentation](https://bixa.app/docs/api)

## 🤔 Need Help?

- [Open an issue](https://github.com/bixacloud/bixa/issues/new) if you've discovered a bug or have a feature request
- Join our [Telegram group](https://t.me/bixacloud) for community support and discussions
- English is the primary language for communication

### 👍 Like Bixa?
If you find Bixa useful, please consider [making a donation](https://bixa.app/DONATE.md) to support its development.

## ©️ Copyright
Code released under [the GPL-2.0 license](LICENSE).
