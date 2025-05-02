$(".navbar-toggler-main-menu").click(function (e) {
    e.preventDefault();
    let target = $(this).attr("data-target");
    $(target).toggleClass("show");
});

$(document).on("click", ".rekkaa-page-link", function (e) {
    e.preventDefault();
    let href = $(this).attr("href");
    $(".modal").modal("hide");
    $("#rekkaa-page-content").html("");

    // handling active menu
    if ($(this).hasClass("menu-link")) {
        $(".menu-item").removeClass("active");
        if ($(this).parent(".menu-item").parent(".menu-sub")) {
            $(this)
                .parent(".menu-item")
                .parent(".menu-sub")
                .parent(".menu-item")
                .addClass("active");
        }
        $(this).parent(".menu-item").addClass("active");
    }
    loadPage(href);
});

$(window).on("popstate", function (e) {
    // console.log(location.href);
    let href = location.href;
    $(".modal").modal("hide");
    if($("#rekkaa-page-content").html()) {
        $("#rekkaa-page-content").html("");
        loadPage(href);
    }
});

function loadPage(href) {
    $(".spinner-box").css({ display: "table" });
    window.history.pushState({ href: href }, "", href);
    $.get(href, function (data) {
        $(".spinner-box").fadeOut();
            // console.log(data);
        if(data) {
            // if($("#rekkaa-page-content").html()) {
                $("#rekkaa-page-content").html(data);

                addLabelReq("#rekkaa-page-content");
            // }
        }
    });
}

function setHtmlTitle(title) {
    $("title").html(title);
}

function checkSwitchFormField(el, value) {
    if(value == "1") {
        if(!$(el).is(":checked")) {
          $(el).click();
        }
      } else {
        if($(el).is(":checked")) {
          $(el).click();
        }
      }
}

function enabledFormField(el, defaultvalue, comparevalue, value, fieldtype) {
    if(defaultvalue == comparevalue) {
        $(el).removeAttr('disabled')
        if(fieldtype == 'select') {
            $(el).val(value).trigger('change');
        } else {
            $(el).val(value);
        }
    } else {
        $(el).attr('disabled', true);
        if(fieldtype == 'select') {
            $(el).val(null).trigger('change');
        } else {
            $(el).val('');
        }
    }
}

$(document).on("keypress", ".nik-input", function (e) {
    let v = $(this).val();
    if (v.length > 15) {
        return false;
    }
    $(this).val(v);
});
$(document).on("keypress", ".npwp-input", function (e) {
    let v = $(this).val();
    if (v.length > 19) {
        return false;
    }

    if (v.length == 2 || v.length == 6 || v.length == 10 || v.length == 16) {
        v += ".";
    }
    if (v.length == 12) {
        v += "-";
    }
    var charCode = e.which ? e.which : e.keyCode;
    if (charCode > 31 && (charCode < 45 || charCode > 57)) {
        return false;
    }
    $(this).val(v);
});

$(document).on("change", ".npwp-input", function (e) {
    let v = $(this).val();
    if (v.length > 19) {
        return false;
    }
    const pair = Array.from(v)
    pair.splice(2, 0, '.');
    pair.splice(6, 0, '.');
    pair.splice(10, 0, '.');
    pair.splice(15, 0, '.');
    pair.splice(12, 0, '-');
    // console.log(pair.join(''))
    $(this).val(pair.join(''));
});

$('.btn-redirect').click(function(e) {
    e.preventDefault();
    let url = $(this).attr("data-url");
    window.open(url, '_blank');
})

// $(document).on("change", ".npwp-input", function (e) {
//     // 41.214.539.3-655.000
//     let v = $(this).val();
//     if (v.length > 19) {
//         return false;
//     }

//     if (v.length == 2 || v.length == 6 || v.length == 10 || v.length == 16) {
//         v += ".";
//     }
//     if (v.length == 12) {
//         v += "-";
//     }
//     var charCode = e.which ? e.which : e.keyCode;
//     // console.log('charCode', charCode)
//     if (charCode > 31 && (charCode < 45 || charCode > 57)) {
//         return false;
//     }
//     $(this).val(v);
// });

addLabelReq();
// addLabelReq(".modal");
function addLabelReq(parentEl = "") {
    $(`${parentEl} .form-lbl-dot .lbl-req`).append(
        '<span class="text-danger">*</span>'
    );
    // $(`${parentEl} .form-lbl-dot label`)
    //     .not(".form-check-label")
    //     .not(".nodot-label")
    //     .append('<span class="float-right">:</span>');
}

// handling sweet alert style
Swal = Swal.mixin({
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "Tidak",
    confirmButtonText: "Ya",
    customClass: {
        confirmButton: "btn btn-sm btn-warning mr-1",
        cancelButton: "btn btn-sm btn-outline-danger mr-1",
    },
    buttonsStyling: false,
    showClass: {
        popup: "animate__animated animate__fadeInDown",
    },
    hideClass: {
        popup: "animate__animated animate__fadeOutUp",
    },
});

function formatCurrency(number) {
    number = parseFloat(number);
    return new Intl.NumberFormat("id-ID").format(number);
}

function resetForm(formEl, others = []) {
    $(formEl).find("select").val(null).trigger("change");
    $(formEl).find("[type=text],[type=hidden],[type=password],[type=email],[type=number], textarea").val("");
    others.forEach((oth) => {
        oth = null;
    });
}

function formatStateObjekPajak(state) {
    if (!state.id) {
        return state.text;
    }
    // console.log('state.text', state.text)
    $stateCustom = $(
        "<span><b>[" + state.id + "]</b> " + state.text + "</span>"
    );
    // console.log('stateCustom', $stateCustom)

    return $stateCustom;
}

function bulan(idx = null) {
    let bulan = [
        {
            id: 1,
            text: "Januari",
        },
        { id: 2, text: "Pebruari" },
        { id: 3, text: "Maret" },
        { id: 4, text: "April" },
        { id: 5, text: "Mei" },
        { id: 6, text: "Juni" },
        { id: 7, text: "Juli" },
        { id: 8, text: "Agustus" },
        { id: 9, text: "September" },
        { id: 10, text: "Oktober" },
        { id: 11, text: "Nopember" },
        { id: 12, text: "Desember" },
    ];

    if (idx) {
        let bln = bulan.filter((bln) => {
            return bln.id == idx;
        });
        return bln[0];
    }

    return bulan;
}
if($.fn.datepicker.dates != undefined) {
$.fn.datepicker.dates["en"] = {
    days: [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
    ],
    daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
    months: [
        "Januari",
        "Pebruari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "Nopember",
        "Desember",
    ],
    monthsShort: [
        "Jan",
        "Peb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nop",
        "Des",
    ],
    today: "Today",
    clear: "Clear",
    format: "mm/dd/yyyy",
    titleFormat: "MM yyyy" /* Leverages same syntax as 'format' */,
    weekStart: 0,
};
}

function findGetParameter(parameterName, itm = "", url = true) {
    var result = null,
        tmp = [];

    var items = [];
    if (url) {
        items = location.search.substr(1).split("&");
    } else {
        items = itm.split("&");
    }
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}

function loadCetakKalkulator(currentUrl, params) {
    let url = `${currentUrl}${params}`;
    $("#backDropCetakModal iframe").attr("src", url);
    $("#backDropCetakModal").attr("data-params", params);

    $("#backDropCetakModal").modal("show");
}

function formatStateAddon(state) {
    // console.log('state', $(state.element).attr('data-price'))
    let price = $(state.element).attr('data-price');
    if (!state.id) {
        return state.text;
    }
    // console.log('state.text', state.text)
    $stateCustom = $(
        `<span>${state.text}<br><b>Rp. ${formatCurrency(price)}</b></span>`
    );
    // console.log('stateCustom', $stateCustom)

    return $stateCustom;
}

function totalAddon() {
    let priceAddon = 0;
    let discountStorage = (localStorage.getItem('discount')) ? JSON.parse(localStorage.getItem('discount')) : null;
    let listaddon = (localStorage.getItem('listaddon')) ? JSON.parse(localStorage.getItem('listaddon')) : [];
    
    listaddon.forEach(ad => priceAddon += Number.parseInt(ad.price) * Number.parseInt(ad.qty));
    $(".addon-plan-total").text(`Rp. ${formatCurrency(priceAddon)}`)

    let discount = 0;
    let qty = 1;
    if(discountStorage && discountStorage.value) {
        console.log('discountStorage.value', discountStorage.value)
        discount = (discountStorage.type == 'TETAP') ? discountStorage.value : subscriptionPrice * parseInt(discountStorage.value) / 100;
        qty = discountStorage.period;
    }
    console.log('subscriptionPrice', subscriptionPrice)
    console.log('discount', discount)
    console.log('discountStorage', discountStorage)
    let newPrice = subscriptionPrice - discount;
    let totalPrice = priceAddon + (newPrice * qty);
    // console.log('newPrice', newPrice);
    $('.qty-plan-total').text(qty);
    $(".pricing-plan-newprice").text(`Rp. ${formatCurrency(newPrice)}`)
    $(".pricing-plan-oldprice").text(``)
    $(".plan-diskon").text(``)
    if(discount > 0) {
        let totalDiscount = discount * qty;
        $(".pricing-plan-oldprice").text(`Rp. ${formatCurrency(subscriptionPrice)}`)
        $(".plan-diskon").text(`Anda menghemat Rp. ${formatCurrency(totalDiscount)}`)
    }
    $(".plan-total").text(`Rp. ${formatCurrency(totalPrice)}`)
}

function generateInputAddon(data) {
    // console.log('data', data)
    let min = '1';
    let btndel = `<br><i class="bx bx-trash me-2 text-danger float-right btn-addon-item-delete" style="cursor: pointer"></i>`;
    if (data.readonly != undefined) {
        min = data.qty; 
        btndel = '';  
    };
    let total = Number.parseInt(data.qty) * Number.parseInt(data.price);
    let el = `<li class="list-group-item align-items-center" data-id="${data.id}">
        <div class="row">
      <div class="col-sm-4">
      ${data.text} 
      </div>
      <div class="col-sm-2">
          <span data-price="${data.price}">Rp. ${formatCurrency(data.price)}</span>
      </div>
      <div class="col-sm-1">
          x
      </div>
      <div class="col-sm-2">
          <input type="number" min="${min}" style="width:45px;padding:5px" value="${data.qty}" class="form-control addon-item-qty">
      </div>
      <div class="col-sm-3">
        <span style="font-weight: bold;" class="addon-item-total">Rp. ${formatCurrency(total)}</span>
        ${btndel}
      </div></div>
    </li>`;
    return el;
}

function generateElAddon(data) {
    // console.log('data', data)
    let total = Number.parseInt(data.qty) * Number.parseInt(data.price);
    let el = `<li class="list-group-item align-items-center" data-id="${data.id}">
        <div class="row">
      <div class="col-sm-4">
      ${data.text} 
      </div>
      <div class="col-sm-2">
          <span data-price="${data.price}">Rp. ${formatCurrency(data.price)}</span>
      </div>
      <div class="col-sm-1">
          x
      </div>
      <div class="col-sm-2">
          ${data.qty}
      </div>
      <div class="col-sm-3">
        <span style="font-weight: bold;" class="addon-item-total">Rp. ${formatCurrency(total)}</span>
      </div></div>
    </li>`;
    return el;
}

$.extend($.validator.messages, {
    required: "Inputan wajib diisi.",
    remote: "Please fix this field.",
    email: "Format email tidak sesuai.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Format angka tidak sesuai.",
    digits: "Please enter only digits.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Panjang inputan tidak boleh lebih dari {0} karakter."),
    minlength: jQuery.validator.format("Panjang inputan tidak boleh kurang dari {0} karakter."),
    rangelength: jQuery.validator.format("Panjang inputan antara {0} dan {1} karakter."),
    range: jQuery.validator.format("Inputan diantara {0} dan {1}."),
    max: jQuery.validator.format("Inputan tidak boleh lebih dari {0}."),
    min: jQuery.validator.format("Inputan tidak boleh kurang dari {0}.")
});

// Add this somewhere before the ajax
var select2GroupBy = function(xs, key) {
    return xs.reduce(function(rv, x) {
        (rv[x[key]] = rv[x[key]] || []).push(x);
        return rv;
    }, {});
};
