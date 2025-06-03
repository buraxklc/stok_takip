<?php
// app/views/purchases/create.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Yeni Alım Oluştur</h3>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo BASE_URL; ?>/purchase/create" method="POST" id="purchaseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="invoice_no" class="form-label">Fatura No</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo $data['invoice_no']; ?>" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Tedarikçi</label>
                                <select class="form-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">Tedarikçi Seçin</option>
                                    <?php foreach($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name'] . ' (' . $supplier['code'] . ')'; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purchase_date" class="form-label">Alım Tarihi</label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $data['today']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Vade Tarihi</label>
                                <input type="date" class="form-control" id="due_date" name="due_date">
                                <div class="form-text">Opsiyonel, vadeli alım için vade tarihini belirtin.</div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5>Ürünler</h5>
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="productTable">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th width="100">Miktar</th>
                                        <th width="150">Birim Fiyat</th>
                                        <th width="100">İndirim %</th>
                                        <th width="100">KDV %</th>
                                        <th width="150">Toplam</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="productRows">
                                    <tr class="product-row">
                                        <td>
                                            <select class="form-select product-select" name="product_id[]" required>
                                                <option value="">Ürün Seçin</option>
                                                <?php foreach($data['products'] as $product): ?>
                                                    <option value="<?php echo $product['id']; ?>" 
                                                            data-price="<?php echo $product['purchase_price']; ?>"
                                                            data-unit="<?php echo $product['unit']; ?>">
                                                        <?php echo $product['name'] . ' (' . $product['code'] . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control quantity" name="quantity[]" min="1" value="1" required>
                                                <span class="input-group-text unit">Adet</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control unit-price" name="unit_price[]" step="0.01" min="0" value="0" required>
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control discount-rate" name="discount_rate[]" min="0" max="100" value="0">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control tax-rate" name="tax_rate[]" min="0" max="100" value="18">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control row-total" value="0" readonly>
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <button type="button" class="btn btn-sm btn-success" id="addRow">
                                                <i class="fas fa-plus"></i> Ürün Ekle
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="note" class="form-label">Not</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Özet</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                              <div class="col-6">Ara Toplam:</div>
                                            <div class="col-6 text-end" id="subtotal">0,00 ₺</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">İndirim:</div>
                                            <div class="col-6 text-end" id="discount">0,00 ₺</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">KDV:</div>
                                            <div class="col-6 text-end" id="tax">0,00 ₺</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Genel Toplam:</strong></div>
                                            <div class="col-6 text-end"><strong id="total">0,00 ₺</strong></div>
                                        </div>
                                        <hr>
                                        <div class="row mb-2">
                                            <div class="col-6">Ödenen Tutar:</div>
                                            <div class="col-6">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="paid_amount" name="paid_amount" min="0" step="0.01" value="0">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6"><strong>Kalan Tutar:</strong></div>
                                            <div class="col-6 text-end"><strong id="due">0,00 ₺</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo BASE_URL; ?>/purchase" class="btn btn-secondary me-md-2">İptal</a>
                            <button type="submit" class="btn btn-primary">Alım Oluştur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ürün seçildiğinde
    function updateProductRow(row) {
        const select = row.querySelector('.product-select');
        const unitText = row.querySelector('.unit');
        const unitPrice = row.querySelector('.unit-price');
        
        if (select.selectedIndex > 0) {
            const option = select.options[select.selectedIndex];
            const price = option.getAttribute('data-price');
            const unit = option.getAttribute('data-unit');
            
            unitPrice.value = price;
            unitText.textContent = unit;
            
            calculateRowTotal(row);
        }
    }
    
    // Satır toplamı hesapla
    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const discountRate = parseFloat(row.querySelector('.discount-rate').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;
        
        const subtotal = quantity * unitPrice;
        const discount = subtotal * (discountRate / 100);
        const afterDiscount = subtotal - discount;
        const tax = afterDiscount * (taxRate / 100);
        const total = afterDiscount + tax;
        
        row.querySelector('.row-total').value = total.toFixed(2);
        
        calculateTotals();
    }
    
    // Genel toplamları hesapla
    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;
        
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const discountRate = parseFloat(row.querySelector('.discount-rate').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;
            
            const rowSubtotal = quantity * unitPrice;
            const rowDiscount = rowSubtotal * (discountRate / 100);
            const afterDiscount = rowSubtotal - rowDiscount;
            const rowTax = afterDiscount * (taxRate / 100);
            
            subtotal += rowSubtotal;
            totalDiscount += rowDiscount;
            totalTax += rowTax;
        });
        
        const total = subtotal - totalDiscount + totalTax;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const due = total - paidAmount;
        
        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' ₺';
        document.getElementById('discount').textContent = totalDiscount.toFixed(2) + ' ₺';
        document.getElementById('tax').textContent = totalTax.toFixed(2) + ' ₺';
        document.getElementById('total').textContent = total.toFixed(2) + ' ₺';
        document.getElementById('due').textContent = due.toFixed(2) + ' ₺';
    }
    
    // Yeni ürün satırı ekle
    document.getElementById('addRow').addEventListener('click', function() {
        const rowTemplate = document.querySelector('.product-row').cloneNode(true);
        rowTemplate.querySelectorAll('input').forEach(input => {
            input.value = input.type === 'number' ? (input.classList.contains('tax-rate') ? 18 : 0) : '';
        });
        rowTemplate.querySelector('.product-select').selectedIndex = 0;
        rowTemplate.querySelector('.unit').textContent = 'Adet';
        
        document.getElementById('productRows').appendChild(rowTemplate);
        
        // Yeni eklenen satır için event listener'ları ayarla
        setupEventListeners(rowTemplate);
    });
    
    // Ürün satırını kaldır
    function setupRemoveRow() {
        document.querySelectorAll('.remove-row').forEach(button => {
            button.addEventListener('click', function() {
                if (document.querySelectorAll('.product-row').length > 1) {
                    this.closest('.product-row').remove();
                    calculateTotals();
                } else {
                    alert('En az bir ürün satırı olmalıdır.');
                }
            });
        });
    }
    
    // Event listener'ları ayarla
    function setupEventListeners(row) {
        row.querySelector('.product-select').addEventListener('change', function() {
            updateProductRow(row);
        });
        
        row.querySelectorAll('.quantity, .unit-price, .discount-rate, .tax-rate').forEach(input => {
            input.addEventListener('input', function() {
                calculateRowTotal(row);
            });
        });
        
        row.querySelector('.remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                alert('En az bir ürün satırı olmalıdır.');
            }
        });
    }
    
    // Ödenen tutar değiştiğinde
    document.getElementById('paid_amount').addEventListener('input', calculateTotals);
    
    // İlk satır için event listener'ları ayarla
    setupEventListeners(document.querySelector('.product-row'));
    
    // Satır kaldırma butonları için event listener'ları ayarla
    setupRemoveRow();
    
    // Form gönderilmeden önce kontrol
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        let hasProducts = false;
        
        document.querySelectorAll('.product-select').forEach(select => {
            if (select.value) {
                hasProducts = true;
            }
        });
        
        if (!hasProducts) {
            e.preventDefault();
            alert('En az bir ürün seçmelisiniz.');
        }
    });
});
</script>

<?php require_once 'app/views/layouts/footer.php'; ?>