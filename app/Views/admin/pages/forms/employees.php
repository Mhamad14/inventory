<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('employees', 'Employees') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/employees'); ?>" data-toggle="tooltip" data-bs-placement="bottom" title="<?= labels('employees_list', 'employees') ?>">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?= labels($from_title) ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/employees/create') ?>" method="post" id="employee_form" class="form-submit-event">
                        <?= csrf_field() ?>
                        <?php if (isset($fetched_data[0]['id'])) { ?>
                            <input type="hidden" name="edit_attribute_set" value="<?= @$fetched_data[0]['id'] ?>">
                        <?php } ?>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="form-group">
                                    <label for="name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= !empty($employee['name']) ? $employee['name'] : "" ?>" required>
                                    <input type="hidden" id="busniess_id" name="busniess_id" value="<?=  $_SESSION['business_id']  ?>">
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
                                        <input type="text" class="form-control phone-number" placeholder="Enter Your Mobile Number" id="contact_number" name="contact_number" value="<?= !empty($employee['contact_number']) ? $employee['contact_number'] : "" ?>" required>
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
                                    <select class="form-control select2" id="position_id" name="position_id">
                                        <option value=""><?= labels('select_position', 'Select Position') ?></option>
                                        <?php foreach ($positions as $position) { ?>
                                            <option value="<?= $position['id'] ?>" <?= !empty($employee['position_id']) && $employee['position_id'] == $position['id'] ? "selected" : "" ?>><?= $position['name'] ?></option>
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
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary"><?= labels('save', 'Save') ?></button>
                            <button type="reset" value="Reset" class="reset btn btn-info" onclick="return resetForm(this.form);"><?= labels('reset', 'Reset') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>