<!-- Start main content wrapper -->
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">SitePro Settings</h4>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?= form_open('api/settings/sitepro') ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="form-label">Hostname</label>
                                    <input type="text" name="hostname" class="form-control mb-2" value="<?= $this->sp->get_hostname() ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control mb-2" value="<?= $this->sp->get_username() ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Password</label>
                                    <input type="text" name="password" class="form-control mb-2" value="<?= $this->sp->get_password() ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-control mb-2" name="status">
                                        <option value="1" <?= $this->sp->get_status() === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= $this->sp->get_status() !== 'active' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <input type="submit" name="update_sitepro" value="Change" class="btn btn-primary waves-effect waves-light">
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end main content wrapper -->