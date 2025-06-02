// public/js/main.js

$(document).ready(function() {
    // Sidebar toggle
    $('[data-widget="pushmenu"]').on('click', function() {
        $('body').toggleClass('sidebar-open');
    });
    
    // Dropdown menüleri
    $('.dropdown-toggle').dropdown();
    
    // Tooltip'leri etkinleştir
    $('[data-toggle="tooltip"]').tooltip();
    
    // Alert mesajlarını otomatik kapat
    setTimeout(function() {
        $('.alert-dismissible').alert('close');
    }, 5000);
    
    // Ürün resmi önizleme
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
        
        // Resim önizleme
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.product-image-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Tablo sıralama
    $('.sortable').on('click', function() {
        var table = $(this).parents('table').eq(0);
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
        this.asc = !this.asc;
        if (!this.asc) {
            rows = rows.reverse();
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i]);
        }
    });
    
    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index);
            var valB = getCellValue(b, index);
            return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
        }
    }
    
    function getCellValue(row, index) {
        return $(row).children('td').eq(index).text();
    }
    
    // Satış ve alım formu ürün ekleme
    $('.add-product-row').on('click', function() {
        var productRow = $('.product-row:first').clone();
        productRow.find('input, select').val('');
        productRow.find('.quantity, .unit-price').trigger('change');
        $('.product-rows').append(productRow);
    });
    
    // Satış ve alım formu ürün silme
    $(document).on('click', '.remove-product-row', function() {
        if ($('.product-row').length > 1) {
            $(this).closest('.product-row').remove();
            calculateTotal();
        }
    });
    
    // Satış ve alım formunda toplam hesaplama
    $(document).on('change keyup', '.quantity, .unit-price, .discount-rate, .tax-rate', function() {
        calculateRowTotal($(this).closest('.product-row'));
        calculateTotal();
    });
    
    function calculateRowTotal(row) {
        var quantity = parseFloat(row.find('.quantity').val()) || 0;
        var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        var discountRate = parseFloat(row.find('.discount-rate').val()) || 0;
        var taxRate = parseFloat(row.find('.tax-rate').val()) || 0;
        
        var subtotal = quantity * unitPrice;
        var discountAmount = subtotal * (discountRate / 100);
        var afterDiscount = subtotal - discountAmount;
        var taxAmount = afterDiscount * (taxRate / 100);
        var total = afterDiscount + taxAmount;
        
        row.find('.row-total').val(total.toFixed(2));
    }
    
    function calculateTotal() {
        var totalAmount = 0;
        var totalDiscount = 0;
        var totalTax = 0;
        
        $('.product-row').each(function() {
            var quantity = parseFloat($(this).find('.quantity').val()) || 0;
            var unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
            var discountRate = parseFloat($(this).find('.discount-rate').val()) || 0;
            var taxRate = parseFloat($(this).find('.tax-rate').val()) || 0;
            
            var subtotal = quantity * unitPrice;
            var discountAmount = subtotal * (discountRate / 100);
            var afterDiscount = subtotal - discountAmount;
            var taxAmount = afterDiscount * (taxRate / 100);
            
            totalAmount += subtotal;
            totalDiscount += discountAmount;
            totalTax += taxAmount;
        });
        
        var netAmount = totalAmount - totalDiscount + totalTax;
        
        $('#total-amount').val(totalAmount.toFixed(2));
        $('#discount-amount').val(totalDiscount.toFixed(2));
        $('#tax-amount').val(totalTax.toFixed(2));
        $('#net-amount').val(netAmount.toFixed(2));
        
        // Kalan tutar hesapla
        var paidAmount = parseFloat($('#paid-amount').val()) || 0;
        var dueAmount = netAmount - paidAmount;
        $('#due-amount').val(dueAmount.toFixed(2));
        
        // Ödeme durumunu güncelle
        if (paidAmount <= 0) {
            $('#payment-status').val('unpaid');
        } else if (paidAmount < netAmount) {
            $('#payment-status').val('partially_paid');
        } else {
            $('#payment-status').val('paid');
        }
    }
    
    // Ödeme tutarı değiştiğinde kalan tutarı güncelle
    $(document).on('change keyup', '#paid-amount', function() {
        calculateTotal();
    });
    
    // Ürün seçildiğinde fiyat ve vergi bilgilerini getir
    $(document).on('change', '.product-id', function() {
        var productId = $(this).val();
        var row = $(this).closest('.product-row');
        
        if (productId) {
            $.ajax({
                url: '/product/getProductData/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    row.find('.unit-price').val(data.sale_price);
                    row.find('.tax-rate').val(data.tax_rate || 0);
                    row.find('.quantity, .unit-price').trigger('change');
                }
            });
        }
    });
    
    // Müşteri seçildiğinde borç bilgisini getir
    $('#customer-id').on('change', function() {
        var customerId = $(this).val();
        
        if (customerId) {
            $.ajax({
                url: '/customer/getCustomerData/' + customerId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#customer-balance').text(data.balance.toFixed(2) + ' ₺');
                }
            });
        }
    });
    
    // Tedarikçi seçildiğinde borç bilgisini getir
    $('#supplier-id').on('change', function() {
        var supplierId = $(this).val();
        
        if (supplierId) {
            $.ajax({
                url: '/supplier/getSupplierData/' + supplierId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#supplier-balance').text(data.balance.toFixed(2) + ' ₺');
                }
            });
        }
    });
    
    // Rapor tarih filtreleme
    $('#date-filter').on('change', function() {
        var value = $(this).val();
        
        if (value === 'custom') {
            $('#custom-date-range').removeClass('d-none');
        } else {
            $('#custom-date-range').addClass('d-none');
        }
    });
    
    // Tarih aralığı seçici
    $('.date-picker').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        language: 'tr'
    });
});