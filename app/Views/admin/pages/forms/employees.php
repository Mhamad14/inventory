<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('employees', 'Employees') ?></h1>
            <div class="section-header-breadcrumb">
                <a class="btn btn-primary text-white" href="<?= base_url('admin/employees'); ?>" data-toggle="tooltip" title="<?= labels('employees', 'Employees') ?>">
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
                    <form action="<?= base_url('admin/employees/create') ?>" method="post" id="employee_form">
                        <?= csrf_field() ?>
                        <?php if (!empty($employee['id'])) { ?>
                            <input type="hidden" name="edit_attribute_set" value="<?= $employee['id'] ?>">
                        <?php } ?>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= !empty($employee['name']) ? $employee['name'] : "" ?>" required>
                                    <input type="hidden" id="busniess_id" name="busniess_id" value="<?= $_SESSION['business_id']  ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="contact_number"><?= labels('mobile_number', 'Mobile') ?></label><span class="asterisk text-danger"> *</span>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control phone-number" placeholder=" <?= labels('enter_mobile', 'Enter Your Mobile Number') ?>" id="contact_number" name="contact_number" value="<?= !empty($employee['contact_number']) ? $employee['contact_number'] : "" ?>" required>
                                    </div>
                                    <span class="text-danger text-bold phone-number-error-message"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="salary"><?= labels('salary', 'Salary') ?></label>
                                    <input type="number" min="0.00" class="form-control" id="salary" name="salary" value="<?= !empty($employee['salary']) ? $employee['salary'] : "" ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="position"><?= labels('position', 'Position') ?></label>
                                    <select class="form-control select2" id="position_id" name="position_id" required>
                                        <option value=""><?= labels('select_position', 'Select Position') ?></option>
                                        <?php foreach ($positions as $position) { ?>
                                            <option value="<?= $position['id'] ?>" <?= !empty($employee['position_id']) && $employee['position_id'] == $position['id'] ? "selected" : "" ?>>
                                                <?= $position['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-8 mb-3">
                                <div class="form-group">
                                    <label for="address"><?= labels('address', 'Address') ?></label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?= !empty($employee['address']) ? $employee['address'] : "" ?>">
                                </div>
                            </div>
                            <input type="hidden" name="updated_at" value="<?= !empty($employee['updated_at']) ? $employee['updated_at'] : date('Y-m-d H:i:s') ?>">
                        </div>
                        <div class="form-group mt-3">
                            <?php if (!empty($employee['id'])) { ?>
                                <button type="submit" name="update" class="btn btn-success" onclick="return confirm('<?= labels('confirm_update_employee', 'Are you sure you want to update this employee?') ?>');"><?= labels('update', 'Update') ?></button>
                                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('<?= labels('confirm_delete_employee', 'Are you sure you want to delete this employee?') ?>');"><?= labels('delete', 'Delete') ?></button>
                            <?php } else { ?>
                                <button type="reset" class="reset btn btn-primary"><?= labels('reset', 'Reset') ?></button>
                                <button type="submit" name="insert" class="btn btn-success" onclick="return confirm('<?= labels('confirm_save_employee', 'Are you sure you want to save this employee?') ?>');"><?= labels('save', 'Save') ?></button>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
