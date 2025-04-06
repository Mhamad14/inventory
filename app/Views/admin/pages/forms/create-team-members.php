<!DOCTYPE html>
<html>

<head>
    <title>Create Team members</title>
    <!-- Include necessary CSS and JavaScript files -->
    <!-- ... -->
</head>

<body>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Create Team members</h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/team_members'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title=" Team members "   ><i class="fas fa-list"></i> </a>
                    </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has('message')) { ?>
                <div class="text-danger">
                    <?php
                    $message = session('message');
                    echo $message;
                    ?>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" id="team_members_formss" action="<?= base_url('admin/team_members/save'); ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="first_name">
                                                First Name
                                                <small class="text-danger">*</small></label>
                                            <input id="first_name" type="text" class="form-control" name="first_name" autofocus>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <small class="text-danger">*</small></label>
                                            <input id="last_name" type="text" class="form-control" name="last_name">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Email Address <small class="text-danger">*</small></label>
                                            <input id="email" type="text" class="form-control" name="email">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="identity">Mobile Number <small class="text-danger">*</small></label>
                                            <input type="text" id="identity" class="form-control phone-number" name="identity">
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="role" class="control-label">Role <span class='text-danger text-sm'>*</span></label>
                                            <select class="form-control system-user-role" name="role">
                                                <option value="">---Select role---</option>
                                                <option value="1" <?= (isset($fetched_data[0]['role']) && $fetched_data[0]['role'] == '1') ? 'selected' : '' ?>>Staff</option>
                                                <option value="2" <?= (isset($fetched_data[0]['role']) && $fetched_data[0]['role'] == '2') ? 'selected' : '' ?>>Editor</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="col-md-12">

                                        <?php if (!empty($businesses)) { ?>
                                            <label for=""><?= labels('check_business', 'Check Business for Delivery Boy') ?></label><span class="asterisk text-danger"> *</span><br>

                                            <?php foreach ($businesses as $business) { ?>
                                                <div class="form-check form-check-inline business mb-2">
                                                    <input class="form-check-input" type="checkbox" name="business_id[]" id="<?= $business['id'] ?>" value="<?= $business['id'] ?>">
                                                    <label class="form-check-label" for="<?= $business['id'] ?>"><?= $business['name'] ?></label>
                                                </div>
                                        <?php }
                                        } ?>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="password" class="d-block">Password</label>
                                            <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password">
                                            <div id="pwindicator" class="pwindicator">
                                                <div class="bar"></div>
                                                <div class="label"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="password_confirm" class="d-block"> Confirm Password</label>
                                            <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg col-md-5" value="user">Register</button>
                                            <button type="reset" class="btn btn-info btn-lg col-md-5" value="user">Reset</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <!-- Permissions Table -->

                            <table class="table permission-table">
                                <tr>
                                    <th>Module/Permissions</th>
                                    <?php foreach ($actions as $action) : ?>
                                        <th><?= ucfirst(str_replace("can_", "",  $action)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <?php foreach ($all_permissions as $permission_module => $permissions) : ?>

                                    <?php
                                    // echo "<pre>";
                                    //    print_r($permissions);
                                    //    die();
                                    ?>
                                    <tr>
                                        <td><?= ucfirst(str_replace("_", " ",  $permission_module))   ?></td>
                                        <?php foreach ($permissions as $action) : ?>


                                            <td>
                                                <label class="custom-switch">
                                                    <input type="checkbox" class="custom-switch-input" name="<?php echo"permissions['$permission_module']['$action']" ?>" >
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description"> <?php  ucfirst(str_replace('can_', '', $action)) ?> </span>
                                                </label>
                                            </td>


                                        <?php endforeach      ?>
                                    </tr>
                                <?php endforeach      ?>
                            </table>
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <div class="d-flex justify-content-center">
                                    <div class="form-group" id="error_box">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" id="submit_btn">Update User</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Include necessary scripts -->
    <!-- ... -->
</body>

</html>