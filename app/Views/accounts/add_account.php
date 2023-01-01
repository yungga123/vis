<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                
                <div class="col-sm-6">
                <?= ($uri->getSegment(1)=='edit-account') ? form_open("post-edit-account",["id" => "form-post-edit-account"]) : form_open("post-add-account",["id" => "form-post-edit-account"]) ?>
                <?= ($uri->getSegment(1)=='edit-account') ? "<input type='hidden' name='id' id='id' value='".$id."'>" : "" ;?>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="employee_id">Employee Name</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">---Please Select---</option>
                                    <?php foreach ($employees as $item) : ?>
                                        <option value="<?= $item['employee_id'] ;?>"><?= $item['employee_id'].' - '.$item['firstname'].' '.$item['lastname'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <small id="small_employee_id" class="text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Enter here...">
                                <small id="small_username" class="text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" name="password" id="password" class="form-control" placeholder="Enter here...">
                                <small id="small_password" class="text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="access_level">Access Level</label>
                                <select name="access_level" id="access_level" class="form-control">
                                    <option value="">---Please Select---</option>
                                    <option value="admin">Administrator</option>
                                    <option value="manager">Managers</option>
                                    <option value="user">User</option>
                                    <option value="sales">Sales</option>
                                    <option value="ofcadmin">Office Admin</option>
                                    <option value="hr">HR</option>
                                </select>
                                <small id="small_access_level" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success float-right"><i class="fas fa-plus-circle"></i> Add Account</button>
                            <a href="<?= site_url('employee-menu') ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Go to Employee Menu</a>
                        </div>
                    </div>

                </div>
                <?= form_close() ?>
            </div>
        </div>
    </section>

</div>