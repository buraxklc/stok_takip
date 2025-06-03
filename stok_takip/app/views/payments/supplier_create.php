<?php
// app/views/payments/supplier_create.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Yeni Tedarikçi Ödemesi Ekle</h3>
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
                    
                    <form action="<?php echo BASE_URL; ?>/payment/createSupplierPayment" method="POST" id="paymentForm">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Tedarikçi</label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Tedarikçi Seçin</option>
                                <?php foreach($data['suppliers'] as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>">
                                        <?php echo $supplier['name'] . ' (' . $supplier['code'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="supplierBalanceInfo" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Tedarikçi Bakiyesi:</strong> <span id="supplierBalance">0,00 ₺</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="purchase_id" class="form-label">Fatura</label>
                            <select class="form-select" id="purchase_id" name="purchase_id">
                                <option value="">Fatura Seçin (Opsiyonel)</option>
                                <?php foreach($data['purchases'] as $purchase): ?>
                                    <?php if($purchase['payment_status'] != 'paid' && $purchase['due_amount'] > 0): ?>
                                        <option value="<?php echo $purchase['id']; ?>" data-supplier-id="<?php echo $purchase['supplier_id']; ?>">
                                            <?php echo $purchase['invoice_no'] . ' - ' . $purchase['supplier_name'] . ' (' . number_format($purchase['due_amount'], 2, ',', '.') . ' ₺)'; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="purchaseInfo" style="display: none;">
                            <div class="alert alert-info">
                                <p><strong>Fatura Tutarı:</strong> <span id="purchaseAmount">0,00 ₺</span></p>
                                <p><strong>Ödenen Tutar:</strong> <span id="purchasePaid">0,00 ₺</span></p>
                                <p><strong>Kalan Tutar:</strong> <span id="purchaseDue">0,00 ₺</span></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Ödeme Tutarı</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">Ödeme Tarihi</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $data['today']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Ödeme Yöntemi</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="cash">Nakit</option>
                                    <option value="bank_transfer">Banka Havalesi</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="check">Çek</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="reference_no" class="form-label">Referans No</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no">
                                <div class="form-text">Opsiyonel, banka referans numarası veya çek numarası girebilirsiniz.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notlar</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Ödeme Ekle</button>
                            <a href="<?php echo BASE_URL; ?>/payment/supplier" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const purchaseSelect = document.getElementById('purchase_id');
    const amountInput = document.getElementById('amount');
    const supplierBalanceInfo = document.getElementById('supplierBalanceInfo');
    const supplierBalance = document.getElementById('supplierBalance');
    const purchaseInfo = document.getElementById('purchaseInfo');
    const purchaseAmount = document.getElementById('purchaseAmount');
    const purchasePaid = document.getElementById('purchasePaid');
    const purchaseDue = document.getElementById('purchaseDue');
    
    // Tedarikçi seçildiğinde
    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;
        
        if(supplierId) {
            // Tedarikçi bakiyesini getir
            fetch('<?php echo BASE_URL; ?>/payment/getSupplierDueAmount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'supplier_id=' + supplierId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    supplierBalanceInfo.style.display = 'block';
                    
                    // Bakiyeyi göster
                    const dueAmount = parseFloat(data.due_amount);
                    supplierBalance.textContent = formatMoney(dueAmount) + ' ₺';
                    
                    // Eğer bakiye yoksa veya eksi bakiye varsa (tedarikçiye borçluysak)
                    if(dueAmount <= 0) {
                        amountInput.max = 0;
                        amountInput.value = 0;
                        // Ödeme yapılabilir, sadece bir uyarı göster
                        supplierBalance.innerHTML += ' <span class="text-warning">(Tedarikçinin borcu yok veya tedarikçiye borçlusunuz)</span>';
                    } else {
                        amountInput.max = dueAmount;
                        amountInput.value = dueAmount.toFixed(2);
                    }
                    
                    // Her zaman ödeme yapılabilir
                    amountInput.disabled = false;
                } else {
                    supplierBalanceInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                supplierBalanceInfo.style.display = 'none';
            });
            
             // Fatura seçimini filtreleme
            const purchaseOptions = purchaseSelect.querySelectorAll('option');
            let hasVisibleOption = false;
            
            purchaseOptions.forEach(option => {
                if(option.value === '') {
                    option.style.display = 'block';
                } else {
                    if(option.getAttribute('data-supplier-id') === supplierId) {
                        option.style.display = 'block';
                        hasVisibleOption = true;
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Eğer görünür fatura yoksa veya sadece boş seçenek varsa, seçimi temizle
            if(!hasVisibleOption) {
                purchaseSelect.value = '';
                purchaseSelect.dispatchEvent(new Event('change'));
                purchaseInfo.style.display = 'none';
            }
        } else {
            supplierBalanceInfo.style.display = 'none';
            purchaseInfo.style.display = 'none';
            amountInput.disabled = false;
            amountInput.value = '';
            amountInput.max = '';
        }
    });
    
    // Fatura seçildiğinde
    purchaseSelect.addEventListener('change', function() {
        const purchaseId = this.value;
        
        if(purchaseId) {
            // Fatura detaylarını getir
            fetch('<?php echo BASE_URL; ?>/payment/getPurchaseDetails', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'purchase_id=' + purchaseId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    purchaseInfo.style.display = 'block';
                    
                    const netAmount = parseFloat(data.purchase.net_amount);
                    const paidAmount = parseFloat(data.purchase.paid_amount);
                    const dueAmount = parseFloat(data.purchase.due_amount);
                    
                    purchaseAmount.textContent = formatMoney(netAmount) + ' ₺';
                    purchasePaid.textContent = formatMoney(paidAmount) + ' ₺';
                    purchaseDue.textContent = formatMoney(dueAmount) + ' ₺';
                    
                    // Ödeme tutarını kalan tutar olarak ayarla
                    if(dueAmount > 0) {
                        amountInput.value = dueAmount.toFixed(2);
                        amountInput.max = dueAmount.toFixed(2);
                    } else {
                        amountInput.value = 0;
                        amountInput.max = 0;
                    }
                    
                    // Tedarikçi seçimini güncelle (seçili değilse)
                    if(supplierSelect.value !== data.purchase.supplier_id) {
                        supplierSelect.value = data.purchase.supplier_id;
                        supplierSelect.dispatchEvent(new Event('change'));
                    }
                } else {
                    purchaseInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                purchaseInfo.style.display = 'none';
            });
        } else {
            purchaseInfo.style.display = 'none';
            
            // Tedarikçi bakiyesini getir
            const supplierId = supplierSelect.value;
            if(supplierId) {
                fetch('<?php echo BASE_URL; ?>/payment/getSupplierDueAmount', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'supplier_id=' + supplierId
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const dueAmount = parseFloat(data.due_amount);
                        if(dueAmount > 0) {
                            amountInput.max = dueAmount.toFixed(2);
                            amountInput.value = dueAmount.toFixed(2);
                        } else {
                            amountInput.max = '';
                            amountInput.value = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    });
    
    // Form gönderilmeden önce kontrol
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const supplierId = supplierSelect.value;
        const amount = parseFloat(amountInput.value);
        
        if(!supplierId) {
            e.preventDefault();
            alert('Lütfen bir tedarikçi seçin.');
            return;
        }
        
        if(isNaN(amount) || amount <= 0) {
            e.preventDefault();
            alert('Lütfen geçerli bir ödeme tutarı girin.');
            return;
        }
        
        // Fatura seçilmişse, ödeme tutarı kalan tutardan büyük olmamalı
        const purchaseId = purchaseSelect.value;
        if(purchaseId && amountInput.max) {
            const maxAmount = parseFloat(amountInput.max);
            if(amount > maxAmount) {
                e.preventDefault();
                alert('Ödeme tutarı, kalan tutardan büyük olamaz. Maximum: ' + maxAmount.toFixed(2) + ' ₺');
                return;
            }
        }
    });
    
    // Para formatı
    function formatMoney(amount) {
        return amount.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+,)/g, '$&.');
    }
});
</script>

<?php require_once 'app/views/layouts/footer.php'; ?>