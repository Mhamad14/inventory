 <div class="row">
     <div class="col-12 col-md-6 col-lg-4">
         <div class="form-group">
             <?php if (!empty($order_type) && $order_type == 'order') { ?>
                 <label for="purchase_date">Purchase Date</label><span class="asterisk text-danger"> *</span>
             <?php } else { ?>
                 <label for="purchase_date">Return Date</label><span class="asterisk text-danger"> *</span>
             <?php } ?>
             <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= isset($purchase['purchase_date']) ? esc($purchase['purchase_date']) : date('Y-m-d') ?>">
         </div>
     </div>
     <div class="col-md-3">
         <div class="form-group">
             <label for="supplier">Supplier</label><span class="asterisk text-danger">*</span>
             <select class="select_supplier form-control" id="supplier" name="supplier_id">
                 <?php if (isset($purchase['supplier_id'])): ?>
                     <option value="<?= esc($purchase['supplier_id']) ?>" selected><?= isset($purchase['supplier_name']) ? esc($purchase['supplier_name']) : 'Selected Supplier' ?></option>
                 <?php endif; ?>
             </select>
         </div>
     </div>

     <div class="col-md-1 supplier-add-btn">
         <span><button class="btn btn-icon btn-secondary edit_btn" data-url="admin/suppliers/create" id=""><i class="fas fa-plus"></i></button></span>
     </div>
     <div class="col-md-3">
         <div class="form-group">
             <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
             <select class=" form-control" id="warehouse_id" name="warehouse_id">
                 <option value="" <?= !isset($purchase['warehouse_id']) ? 'selected' : '' ?>>Select warehouse </option>
                 <?php foreach ($warehouses as $warehouse) { ?>
                     <option value="<?= $warehouse['id'] ?>" <?= (isset($purchase['warehouse_id']) && $purchase['warehouse_id'] == $warehouse['id']) ? 'selected' : '' ?>><?= $warehouse['name'] ?></option>
                 <?php  } ?>
             </select>
         </div>
     </div>
 </div>