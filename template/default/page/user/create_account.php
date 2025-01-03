<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18"><?= $this->base->text('create_host', 'heading') ?></h4>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-10">
                    

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?= $this->base->text('create_host', 'heading') ?></h4>
                        </div>

                        <div class="card-body p-4">
                            <!-- Domain Check Form -->
                            <?= form_open('account/create', ['id' => 'checkDomainForm']) ?>
                                <div class="mb-4">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-lg-6">
                                            <label class="form-label"><?= $this->base->text('domain_name', 'label') ?></label>
                                            <input type="text" name="domain" class="form-control form-control-lg" 
                                                   placeholder="Enter subdomain name" required>
                                        </div>
                                        <div class="col-lg-3">
                                            <select name="ext" class="form-select form-select-lg">
                                                <?php foreach ($this->mofh->list_exts() as $ext): ?>
                                                    <option><?= $ext['domain_name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="hidden" name="check_subdomain" value="1">
                                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                                <i class="fas fa-search me-1"></i> <?= $this->base->text('check_availibilty', 'button') ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?= form_close() ?>

                            <?php if($this->session->userdata('domain')): ?>
                            <div class="account-setup mt-4">
                                <hr>
                                <?= form_open('account/create', ['id' => 'createAccountForm']) ?>
                                    <h5 class="mb-4"><?= $this->base->text('account_conf', 'label') ?></h5>

                                    <div class="alert alert-info mb-4">
                                        <?= $this->base->text('domain', 'label') ?> <strong><?= $this->session->userdata('domain') ?></strong>
                                        <a href="<?= base_url('account/create?cancel=true') ?>" class="float-end"><?= $this->base->text('change', 'button') ?></a>
                                    </div>

                                    <div class="mb-3">
                                      <label class="form-label"><?= $this->base->text('account_label', 'label') ?></label>
                                      <input type="text" name="label" class="form-control form-control-lg" required placeholder="<?= $this->base->text('account_label', 'label') ?>" autocomplete="new-password" data-lpignore="true" data-form-type="other">
                                    </div>
                                  
                                    <?php if($this->grc->is_active()):?>
                                        <div class="mb-4">
                                            <?php if($this->grc->get_type() == "google"):?>
                                                <div class="g-recaptcha" data-sitekey="<?= $this->grc->get_site_key();?>"></div>
                                                <script src='https://www.google.com/recaptcha/api.js' async defer ></script>
                                            <?php elseif($this->grc->get_type() == "crypto"): ?>
                                                <script src='https://verifypow.com/lib/captcha.js' async></script>
                                                <div class='CRLT-captcha' data-hashes='256' data-key='<?= $this->grc->get_site_key();?>'>
                                                    <em>Loading PoW Captcha...<br>If it doesn't load, please disable AdBlocker!</em>
                                                </div>
                                            <?php elseif($this->grc->get_type() == "human"): ?>
                                                <div id='captcha' class='h-captcha' data-sitekey="<?= $this->grc->get_site_key();?>"></div>
                                                <script src='https://hcaptcha.com/1/api.js' async defer></script>
                                            <?php elseif ($this->grc->get_type() == "turnstile") : ?>
                                                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                                                <div class="cf-turnstile" data-sitekey="<?= $this->grc->get_site_key(); ?>" data-callback="javascriptCallback"></div>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
        <input type="hidden" name="create" value="1">

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i> <?= $this->base->text('create_acc', 'button') ?>
                                        </button>
                                    </div>
                                <?= form_close() ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to buttons
    document.getElementById('checkDomainForm')?.addEventListener('submit', function(e) {
        let btn = this.querySelector('button[type="submit"]');
        let icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
        btn.innerHTML = icon + ' Checking...';
        btn.disabled = true;
    });

    document.getElementById('createAccountForm')?.addEventListener('submit', function(e) {
        let btn = this.querySelector('button[type="submit"]');
        let icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
        btn.innerHTML = icon + ' Creating...';  
        btn.disabled = true;
    });
});
</script>