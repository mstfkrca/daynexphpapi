// API Sunucu Adres Tanımlamaları
const PRODUCT_API  = 'http://localhost:8080/index.php/product';
const CATEGORY_API = 'http://localhost:8080/index.php/category';

$(document).ready(function() {
    
    // Toastr Bildirim Ayarları
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Hafızadan Token Kontrolü
    const kayitliToken = localStorage.getItem('daynex_jwt_token');
    if (kayitliToken) {
        $('#jwtTokenInput').val(kayitliToken);
    }

    // JWT Formu Kaydedildiğinde
    $('#jwtForm').on('submit', function(e) {
        e.preventDefault();
        const token = $('#jwtTokenInput').val().trim();
        if (token) {
            localStorage.setItem('daynex_jwt_token', token);
            toastr.success('🔑 JWT Token başarıyla hafızaya kaydedildi!');
        } else {
            toastr.warning('Lütfen geçerli bir token girin.');
        }
    });

    // İlk açılışta verileri çek
    loadAllData();

    // Verileri Yenile Butonu
    $('#btnReloadAll').on('click', function() {
        loadAllData();
        toastr.info('Tüm veriler API üzerinden tazelendi.');
    });

    // KATEGORİ EKLEME
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        const yeniKategoriAdi = $('#catName').val().trim();

        let zatenVarMi = false;
        $('#catSelect option').each(function() {
            if ($(this).text().toLowerCase() === yeniKategoriAdi.toLowerCase()) {
                zatenVarMi = true;
                return false;
            }
        });

        if (zatenVarMi) {
            toastr.warning(`⚠️ "${yeniKategoriAdi}" kategorisi listede zaten mevcut!`);
            return false;
        }

        const catPayload = {
            name: yeniKategoriAdi,
            description: $('#catDesc').val()
        };

        $.ajax({
            url: `${CATEGORY_API}/create`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(catPayload),
            success: function(response) {
                if(response.status) {
                    toastr.success('📁 Kategori başarıyla eklendi!');
                    $('#catName').val('');
                    $('#catDesc').val('');
                    loadAllData();
                } else {
                    toastr.error('Hata: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = "Kategori eklenirken hata oldu!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) errorMessage = parsed.message;
                    } catch (e) {}
                }
                toastr.error(`⚠️ ${errorMessage}`);
            }
        });
    });

    // ÜRÜN EKLEME
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        const productPayload = {
            category_id: parseInt($('#catSelect').val()),
            name: $('#pName').val(),
            price: parseFloat($('#pPrice').val()),
            stock: parseInt($('#pStock').val())
        };

        $.ajax({
            url: `${PRODUCT_API}/create`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(productPayload),
            success: function(response) {
                if(response.status) {
                    toastr.success('📦 Ürün başarıyla eklendi!');
                    $('#pName').val('');
                    loadAllData();
                } else {
                    toastr.error('Hata: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = "Ürün eklenirken hata oluştu!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) errorMessage = parsed.message;
                    } catch (e) {}
                }
                toastr.error(`⚠️ ${errorMessage}`);
            }
        });
    });
});

function loadAllData() {
    fetchLiveCategories();
    fetchLiveProducts();
}

function fetchLiveCategories() {
    $.ajax({
        url: CATEGORY_API,
        type: 'GET',
        dataType: 'json',
        success: function(result) {
            const $select = $('#catSelect');
            $select.empty();
            if (result.status && result.data.length > 0) {
                $.each(result.data, function(index, category) {
                    $select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            } else {
                $select.append('<option value="">Önce Kategori Eklemelisiniz!</option>');
            }
        },
        error: function() {
            $('#catSelect').html('<option value="">Bağlantı Hatası!</option>');
        }
    });
}

function fetchLiveProducts() {
    $.ajax({
        url: PRODUCT_API,
        type: 'GET',
        dataType: 'json',
        success: function(result) {
            const $tbody = $('#productTableBody');
            $tbody.empty();
            if (result.status && result.data.length > 0) {
                $.each(result.data, function(index, product) {
                    $tbody.append(`
                        <tr>
                            <td class="ps-4 text-muted">#${product.id}</td>
                            <td><span class="fw-semibold text-dark">${product.name}</span></td>
                            <td><span class="badge bg-light text-primary border border-primary-subtle px-3">${product.category_name}</span></td>
                            <td class="fw-medium">${product.price} TL</td>
                            <td><span class="text-secondary">${product.stock} Adet</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${product.id}">Kalıcı Sil</button>
                            </td>
                        </tr>
                    `);
                });

                $('.btn-delete').off('click').on('click', function() {
                    const productId = $(this).data('id');
                    deleteSelectedProduct(productId);
                });
            } else {
                $tbody.html('<tr><td colspan="6" class="text-center text-muted py-5">Veritabanında ürün bulunamadı.</td></tr>');
            }
        },
        error: function() {
            $('#productTableBody').html('<tr><td colspan="6" class="text-center text-danger py-5">API sunucusuna bağlanılamadı!</td></tr>');
        }
    });
}

// DOĞRU VE TEKİL SİLME FONKSİYONU
function deleteSelectedProduct(id) {
    const jwtToken = $('#jwtTokenInput').val().trim();

    if (!jwtToken) {
        toastr.warning('🔒 Yetkisiz işlem! Geçerli bir JWT Token yapıştırmalısınız.');
        return false;
    }

    if (confirm('Bu ürünü kalıcı olarak silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: `${PRODUCT_API}/delete/${id}`,
            type: 'DELETE',
            // HEADER ÇİFT DİKİŞ: Tarayıcı veya sunucu harfleri küçültürse diye her iki ihtimali de yolluyoruz!
            headers: {
                'X-Authorization': jwtToken,
                'x-authorization': jwtToken,
                'Authorization': 'Bearer ' + jwtToken
            },
            success: function(response) {
                if (response.status) {
                    toastr.success('🗑️ ' + response.message);
                    loadAllData();
                } else {
                    toastr.warning('Silme İşlemi Reddedildi! ' + response.message);
                }
            },
            error: function(xhr) {
                let errMessage = "Yetkisiz Erişim / Geçersiz Token!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) errMessage = parsed.message;
                    } catch (e) {}
                }
                toastr.error('🔴 GÜVENLİK DUVARI: ' + errMessage);
            }
        });
    }
}