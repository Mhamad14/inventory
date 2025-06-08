<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('positions', 'positions') ?></h1>
            <div class="section-header-breadcrumb">
                <a class="btn btn-primary text-white" href="<?= base_url('admin/positions'); ?>" data-toggle="tooltip" title="<?= labels('positions', 'Positions') ?>">
                    <i class="fas fa-list"></i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?= labels($from_title) ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/positions/create') ?>" method="post" id="position_form">
                        <?= csrf_field() ?>
                        <?php if (!empty($position['id'])) { ?>
                            <input type="hidden" name="edit_attribute_set" value="<?= $position['id'] ?>">
                        <?php } ?>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= !empty($position['name']) ? $position['name'] : "" ?>" required>
                                    <input type="hidden" id="bussness_id" name="business_id" value="<?= $_SESSION['business_id']  ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="description"><?= labels('description', 'description') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="description" name="description" value="<?= !empty($position['description']) ? $position['description'] : "" ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <?php if (!empty($position['id'])) { ?>
                                <button type="submit" name="update" class="btn btn-success" onclick="return confirm('<?= labels('confirm_update_position', 'Are you sure you want to update this position?') ?>');"><?= labels('update', 'Update') ?></button>
                                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('<?= labels('confirm_delete_position', 'Are you sure you want to delete this employee?') ?>');"><?= labels('delete', 'Delete') ?></button>
                            <?php } else { ?>
                                <button type="reset" class="reset btn btn-primary"><?= labels('reset', 'Reset') ?></button>
                                <button type="submit" name="insert" class="btn btn-success" onclick="return confirm('<?= labels('confirm_save_position', 'Are you sure you want to save this employee?') ?>');"><?= labels('save', 'Save') ?></button>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
