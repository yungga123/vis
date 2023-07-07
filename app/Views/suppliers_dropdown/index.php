<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <input type="hidden" id="edit_url" value="" readonly>
    <input type="hidden" id="remove_url" value="" readonly>
    <!-- Suppliers List Card -->
    <div class="card">
        <div class="card-body">


            <table id="" class="table table-bordered table-striped nowrap" data-url="">
                <thead>
                    <tr>
                        
                    </tr>
                </thead>
            </table>


        </div>
    </div>
</div>

<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>