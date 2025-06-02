<?php
// app/views/payments/customer_create.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Yeni Tahsilat Ekle</h3>
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
                    
                    <form action="<?php echo BASE_URL; ?>/payment/createCustomerPayment" method="POST" id="paymentForm">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Müşteri</label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Müşteri Seçin</option>
                                <?php foreach($data['customers'] as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>">
                                        <?php echo $customer['name'] . ' (' . $customer['code'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="customerBalanceInfo" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Müşteri Bakiyesi:</strong> <span id="customerBalance">0,00 ₺</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sale_id" class="form-label">Fatura</label>
                            <select class="form-select" id="sale_id" name="sale_id">
                                <option value="">Fatura Seçin (Opsiyonel)</option>
                                <?php foreach($data['sales'] as $sale): ?>
                                    <?php if($sale['payment_status'] != 'paid' && $sale['due_amount'] > 0): ?>
                                        <option value="<?php echo $sale['id']; ?>" data-customer-id="<?php echo $sale['customer_id']; ?>">
                                            <?php echo $sale['invoice_no'] . ' - ' . $sale['customer_name'] . ' (' . number_format($sale['due_amount'], 2, ',', '.') . ' ₺)'; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="saleInfo" style="display: none;">
                            <div class="alert alert-info">
                                <p><strong>Fatura Tutarı:</strong> <span id="saleAmount">0,00 ₺</span></p>
                                <p><strong>Ödenen Tutar:</strong> <span id="salePaid">0,00 ₺</span></p>
                                <p><strong>Kalan Tutar:</strong> <span id="saleDue">0,00 ₺</span></p>
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
                            <button type="submit" class="btn btn-primary">Tahsilat Ekle</button>
                            <a href="<?php echo BASE_URL; ?>/payment/customer" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const saleSelect = document.getElementById('sale_id');
    const amountInput = document.getElementById('amount');
    const customerBalanceInfo = document.getElementById('customerBalanceInfo');
    const customerBalance = document.getElementById('customerBalance');
    const saleInfo = document.getElementById('saleInfo');
    const saleAmount = document.getElementById('saleAmount');
    const salePaid = document.getElementById('salePaid');
    const saleDue = document.getElementById('saleDue');
    
    // Müşteri seçildiğinde
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        
        if(customerId) {
            // Müşteri bakiyesini getir
            fetch('<?php echo BASE_URL; ?>/payment/getCustomerDueAmount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'customer_id=' + customerId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    customerBalanceInfo.style.display = 'block';
                    
                    // Bakiyeyi göster
                    const dueAmount = parseFloat(data.due_amount);
                    customerBalance.textContent = formatMoney(dueAmount) + ' ₺';
                    
                    // Eğer bakiye yoksa veya eksi bakiye varsa (müşteriye borçluysak)
                    if(dueAmount <= 0) {
                        amountInput.max = 0;
                        amountInput.value = 0;
                        // Ödeme yapılabilir, sadece bir uyarı göster
                        customerBalance.innerHTML += ' <span class="text-warning">(Müşterinin borcu yok veya müşteriye borçlusunuz)</span>';
                    } else {
                        amountInput.max = dueAmount;
                        amountInput.value = dueAmount.toFixed(2);
                    }
                    
                    // Her zaman ödeme yapılabilir
                    amountInput.disabled = false;
                } else {
                    customerBalanceInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                customerBalanceInfo.style.display = 'none';
            });
            
            // Fatura seçimini filtreleme
            const saleOptions = saleSelect.querySelectorAll('option');
            let hasVisibleOption = false;
            
            saleOptions.forEach(option => {
                if(option.value === '') {
                    option.style.display = 'block';
                } else {
                    if(option.getAttribute('data-customer-id') === customerId) {
                        option.style.display = 'block';
                        hasVisibleOption = true;
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Eğer görünür fatura yoksa veya sadece boş seçenek varsa, seçimi temizle
            if(!hasVisibleOption) {
                saleSelect.value = '';
                saleSelect.dispatchEvent(new Event('change'));
                saleInfo.style.display = 'none';
            }
        } else {
            customerBalanceInfo.style.display = 'none';
            saleInfo.style.display = 'none';
            amountInput.disabled = false;
            amountInput.value = '';
            amountInput.max = '';
        }
    });
    
    // Fatura seçildiğinde
    saleSelect.addEventListener('change', function() {
        const saleId = this.value;
        
        if(saleId) {
            // Fatura detaylarını getir
            fetch('<?php echo BASE_URL; ?>/payment/getSaleDetails', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'sale_id=' + saleId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    saleInfo.style.display = 'block';
                    
                    const netAmount = parseFloat(data.sale.net_amount);
                    const paidAmount = parseFloat(data.sale.paid_amount);
                    const dueAmount = parseFloat(data.sale.due_amount);
                    
                    saleAmount.textContent = formatMoney(netAmount) + ' ₺';
                    salePaid.textContent = formatMoney(paidAmount) + ' ₺';
                    saleDue.textContent = formatMoney(dueAmount) + ' ₺';
                    
                    // Ödeme tutarını kalan tutar olarak ayarla
                    if(dueAmount > 0) {
                        amountInput.value = dueAmount.toFixed(2);
                        amountInput.max = dueAmount.toFixed(2);
                    } else {
                        amountInput.value = 0;
                        amountInput.max = 0;
                    }
                    
                    // Müşteri seçimini güncelle (seçili değilse)
                    if(customerSelect.value !== data.sale.customer_id) {
                        customerSelect.value = data.sale.customer_id;
                        customerSelect.dispatchEvent(new Event('change'));
                    }
                } else {
                    saleInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                saleInfo.style.display = 'none';
            });
        } else {
            saleInfo.style.display = 'none';
            
            // Müşteri bakiyesini getir
            const customerId = customerSelect.value;
            if(customerId) {
                fetch('<?php echo BASE_URL; ?>/payment/getCustomerDueAmount', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'customer_id=' + customerId
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
        const customerId = customerSelect.value;
        const amount = parseFloat(amountInput.value);
        
        if(!customerId) {
            e.preventDefault();
            alert('Lütfen bir müşteri seçin.');
            return;
        }
        
        if(isNaN(amount) || amount <= 0) {
            e.preventDefault();
            alert('Lütfen geçerli bir ödeme tutarı girin.');
            return;
        }
        
        // Fatura seçilmişse, ödeme tutarı kalan tutardan büyük olmamalı
        const saleId = saleSelect.value;
        if(saleId && amountInput.max) {
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