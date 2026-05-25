/* ==========================================
   SELLORA AI - FRONTEND LOGIC (GRID ONLY)
========================================== */
jQuery(document).ready(function($) {

    if (typeof sellora_obj !== 'undefined' && sellora_obj.product_id > 0) {
        fetchRecommendations();
    }

    function fetchRecommendations() {
        $.ajax({
            url: sellora_obj.ajax_url,
            type: 'POST',
            data: { 
                action: 'sellora_get_recs', 
                product_id: sellora_obj.product_id 
            },
            success: function(res) {
                if (res.success) {
                    if(res.data.html) {
                        renderGrid(res.data.html, '#sellora-grid-wrapper', '#sellora-grid-container');
                    }
                    if (res.data.fbt_data && res.data.fbt_data.length > 1) {
                        renderFBT(res.data.fbt_data);
                    }
                }
            }
        });
    }

    function renderGrid(htmlData, wrapperId, containerId) {
        // تزریق ساده HTML به همراه کلاس‌های پیش‌فرض ووکامرس برای قالب شما
        $(wrapperId).html('<ul class="products columns-4">' + htmlData + '</ul>');
        $(containerId).fadeIn(400);
    }

    function renderFBT(data) {
        let imagesHtml = '<div class="sellora-fbt-gallery">';
        let listHtml = '<ul class="sellora-fbt-items">';
        let totalPrice = 0;

        data.forEach((item, index) => {
            if(index > 0) imagesHtml += '<span class="sellora-fbt-plus">+</span>';
            imagesHtml += `<a href="${item.permalink}"><img src="${item.image}" alt="${item.title}" /></a>`;
            
            let isDisabled = item.is_main ? 'disabled' : '';
            listHtml += `
                <li>
                    <label>
                        <input type="checkbox" class="sellora-fbt-checkbox" data-id="${item.id}" data-price="${item.price}" checked ${isDisabled}>
                        <span class="fbt-title">${item.title}</span>
                        <span class="fbt-price">${item.price_html}</span>
                    </label>
                </li>
            `;
            totalPrice += item.price;
        });

        imagesHtml += '</div>';
        listHtml += '</ul>';

        const actionBox = `
            <div class="sellora-fbt-action">
                <div class="fbt-total-text">Total Price: <strong id="sellora-fbt-total">${sellora_obj.currency}${totalPrice.toFixed(2)}</strong></div>
                <button id="sellora-fbt-add-btn" class="button alt">${sellora_obj.i18n.b_add}</button>
            </div>
        `;

        const finalHtml = `<h3>${sellora_obj.i18n.t_fbt}</h3>` + imagesHtml + listHtml + actionBox;
        $('#sellora-fbt-wrapper').html(finalHtml).fadeIn(500);
    }

    $(document).on('change', '.sellora-fbt-checkbox', function() {
        let updatedTotal = 0;
        $('.sellora-fbt-checkbox:checked').each(function() { 
            updatedTotal += parseFloat($(this).data('price')); 
        });
        $('#sellora-fbt-total').text(sellora_obj.currency + updatedTotal.toFixed(2));
    });

    $(document).on('click', '#sellora-fbt-btn, #sellora-fbt-add-btn', function(e) {
        e.preventDefault();
        let selectedIds = [];
        
        $('.sellora-fbt-checkbox:checked').each(function() { 
            selectedIds.push($(this).data('id')); 
        });

        if (selectedIds.length === 0) return;

        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text('Adding...').prop('disabled', true);

        $.ajax({
            url: sellora_obj.ajax_url,
            type: 'POST',
            data: { 
                action: 'sellora_add_all', 
                product_ids: selectedIds 
            },
            success: function(res) {
                if(res.success) {
                    window.location.href = sellora_obj.ajax_url.split('wp-admin')[0] + 'cart/';
                } else {
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

});