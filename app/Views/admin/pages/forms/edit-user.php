<!DOCTYPE html>
<html>

<head>
    <title>Edit Team members</title>
</head>

<body>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit User</h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/team_members'); ?>" class="btn"><i class="fas fa-list"></i> Team members</a>
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
            <?php }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('admin/team_members/update_user'); ?>" id="team_members_form" method="POST">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="first_name">First Name <small class="text-danger">*</small></label>
                                            <input id="first_name" type="text" class="form-control" name="first_name" placeholder="" value="<?= isset($user->first_name) ? $user->first_name : 'admin'; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <small class="text-danger">*</small></label>
                                            <input id="last_name" type="text" class="form-control" name="last_name" value="<?= $user->last_name; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Email <small class="text-danger">*</small></label>
                                            <input id="email" type="text" class="form-control" name="email" value="<?= $user->email; ?>">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="identity">Mobile Number <small class="text-danger">*</small></label>
                                            <input type="text" id="identity" class="form-control phone-number" name="identity" value="<?= $user->mobile; ?>">
                                        </div>
                                    </div>


                                    <div class="col-md-12">

                                        <?php if (!empty($businesses_list)) { ?>
                                            <label for=""><?= labels('check_business', 'Check Business for Delivery Boy') ?></label><span class="asterisk text-danger"> *</span><br>

                                            <?php foreach ($businesses_list as $business) {

                                                // Check if the business ID is in the array of checked IDs
                                                $isChecked = in_array($business['id'],  $business_ids) ? 'checked' : '';
                                            ?>
                                                <div class="form-check form-check-inline business mb-2">
                                                    <input class="form-check-input" type="checkbox" name="business_id[]" id="<?= $business['id'] ?>" <?= $isChecked ?> value="<?= $business['id'] ?>">
                                                    <label class="form-check-label" for="<?= $business['id'] ?>"><?= $business['name'] ?></label>
                                                </div>

                                            <?php } ?>

                                        <?php } ?>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="password" class="d-block">Password</label>
                                            <?php if (empty($user->password)) : ?>
                                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password">
                                            <?php else : ?>
                                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" value="">
                                            <?php endif; ?>
                                            <div id="pwindicator" class="pwindicator">
                                                <div class="bar"></div>
                                                <div class="label"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="password_confirm" class="d-block">Confirm Password</label>
                                            <?php if (empty($user->password)) : ?>
                                                <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                                            <?php else : ?>
                                                <input id="password_confirm" type="password" class="form-control" name="password_confirm" value="">
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="<?= $user->id; ?>">
                                            <button type="submit" class="btn btn-primary btn-lg col-md-5" value="user">Update Members</button>
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

                            <table class="table permission-table ">

                                <tr>
                                    <th>Module/Permissions</th>
                                    <?php foreach ($actions as $action) : ?>
                                        <th><?= ucfirst(str_replace("can_", "",  $action)) ?></th>
                                    <?php endforeach;
                                    ?>
                                </tr>
                                <?php foreach ($all_permissions as $permission_name => $permission_actions) :
                                ?>
                                    <tr>
                                        <td> <?= ucfirst(str_replace("_", " ",  $permission_name))    ?></td>
                                        <?php foreach ($permission_actions as $action => $value) : ?>
                                            <?php
                                            $checked = '';
                                          
                                            if (isset($user_permissions["'$permission_name'"]) && in_array("'$value'", (array) $user_permissions["'$permission_name'"])) {
                                                $checked = "checked";
                                                
                                            }
                                            ?>

                                            <td>
                                                <label class="custom-switch">
                                                    <input type="checkbox" class="custom-switch-input" name="<?php echo "permissions['$permission_name']['$value']" ?>" <?php echo $checked  ?>>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description"> <?php ucfirst(str_replace('can_', '', $action)) ?> </span>
                                                </label>
                                            </td>


                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach;

                                ?>
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