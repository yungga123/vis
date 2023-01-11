<!-- Content Wrapper. Contains page content -->
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
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Customer List</h3>
              <!-- Search form -->

              <!-- <input type='text' name='search' value='</?= $search ?>'><input type='button' id='btnsearch' value='Submit' onclick='document.getElementById("searchForm").submit();'> -->
              <div class="card-tools">
                <form method='get' action="customervt-list" id="searchForm">

                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="search" class="form-control float-right" value='<?= $search ?>' placeholder="Search">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default" id='btnsearch' onclick='document.getElementById("searchForm").submit();'>
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="card-body">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Contact Person</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Email Address</th>
                    <th>Source</th>
                    <th>Notes</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($customersVtViewModel as $item) : ?>

                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td><?= $item['id'] ?></td>
                      <td><?= $item['customer_name'] ?></td>
                      <td><?= $item['contact_person'] ?></td>
                      <td><?= $item['address'] ?></td>
                      <td><?= $item['contact_number'] ?></td>
                      <td><?= $item['email_address'] ?></td>
                      <td><?= $item['source'] ?></td>
                      <td><?= $item['notes'] ?></td>
                      <td>
                        <div class="btn-group">
                          <a href="<?= site_url('edit-customervt/').$item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                          <a href="<?= site_url('delete-customervt/').$item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                      </td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="9">
                        <div class="row">
                          <div class="col-12">
                            <div class="card p-0">
                              <div class="card-header">
                                <div class="card-title">Customer Branch</div>

                              </div>
                              <div class="card-body">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Branch</th>
                                      <th>Contact Person</th>
                                      <th>Contact Number</th>
                                      <th>Email Address</th>
                                      <th>Address</th>
                                      <th>Notes</th>
                                      <th>Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach ($customer_find = $customerVtBranchViewModel->where('customer_id', $item['id'])->findAll() as $item_branch) : ?>

                                      <tr>
                                        <td><?= $item_branch['branch_name'] ?></td>
                                        <td><?= $item_branch['contact_person'] ?></td>
                                        <td><?= $item_branch['contact_number'] ?></td>
                                        <td><?= $item_branch['email_address'] ?></td>
                                        <td><?= $item_branch['address'] ?></td>
                                        <td><?= $item_branch['notes'] ?></td>
                                        <td>
                                          <div class="btn-group">
                                            <a href="<?= site_url('edit-customerbranch/').$item_branch['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="<?= site_url('delete-customer-branch/').$item_branch['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                          </div>
                                        </td>
                                      </tr>

                                    <?php endforeach ?>

                                    <?php if (!$customer_find) : ?>
                                      <tr class="text-center text-danger">
                                        <td colspan="6"><i>NO BRANCH YET!!</i></td>
                                      </tr>
                                    <?php endif ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <div class="float-right">
                <?= $pager->makeLinks($page, $perPage, $total, 'custom_view') ?>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>