<!-- Generate PO Modal -->
<div class="modal fade" id="generate_PO_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="generate_po_form" class="with-label-indicator" action="<?= url_to('generate-po.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="form-group">
                  <label for="select_rpf"></label>
                  <select class="form-control" name="select_rpf" id="select_rpf">
                    <option></option>
                    <option></option>
                    <option></option>
                  </select>
                </div>
            </form>
        </div>
    </div>
</div>