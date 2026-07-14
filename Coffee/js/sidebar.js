window.console =
  window.console ||
  (function () {
    var e = {};
    e.log =
      e.warn =
      e.debug =
      e.info =
      e.error =
      e.time =
      e.dir =
      e.profile =
      e.clear =
      e.exception =
      e.trace =
      e.assert =
        function () {};
    return e;
  })();
  $(document).ready(function () {
    var e =
      '<div class="menu-mobile-popup">' +
      '<div class="modal-menu__backdrop clear-fixed"></div> ' +
      '<div class="widget-filter">' +
      '<div class="header-ct-center menu-moblie">' +
      '<div class="sidebar-logo">' +
      '<img src="images/logo/logomenu.png">' +
      "<h2>کافه مانستر</h2>" +
      "</div> " +
      '<ul id="menu-primary-menu" class="menu">' +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">خانه</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-home-1">' +
      '<a href="index.html">صفحه اصلی 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-home-2">' +
      '<a href="home-02.html">صفحه اصلی 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-home-3">' +
      '<a href="home-03.html">صفحه اصلی 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-home-4">' +
      '<a href="home-04.html">صفحه اصلی 04 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-home-5">' +
      '<a href="home-05.html">صفحه اصلی 05 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-home-6">' +
      '<a href="home-06.html">صفحه اصلی 06 </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">فیلتر و جستجو</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-search">' +
      '<a href="filter-search-01.html">جستجو 01</a>' +
      "</li>" +
      '<li class="menu-item-mobile current-search2">' +
      '<a href="filter-search-02.html">جستجو 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-search3">' +
      '<a href="filter-search-03.html">جستجو 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-searchResult">' +
      '<a href="search-result.html">نتایج جستجو 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-searchResult2">' +
      '<a href="search-result-02.html">نتایج جستجو 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-searchResult3">' +
      '<a href="search-result-03.html">نتایج جستجو 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-searchResult4">' +
      '<a href="search-result-04.html">نتایج جستجو 04 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-filter">' +
      '<a href="filter.html">فیلتر </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">جزئیات محصول</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-details">' +
      '<a href="product-details-1.html">جزئیات محصول 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details2">' +
      '<a href="product-details-2.html">جزئیات محصول 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details3">' +
      '<a href="product-details-3.html">جزئیات محصول 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details4">' +
      '<a href="product-details-4.html">جزئیات محصول 04 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details5">' +
      '<a href="product-details-5.html">جزئیات محصول 05 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details6">' +
      '<a href="product-details-6.html">جزئیات محصول 06 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details7">' +
      '<a href="product-details-7.html">جزئیات محصول 07 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details8">' +
      '<a href="product-details-8.html">جزئیات محصول 08 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details9">' +
      '<a href="product-details-9.html">جزئیات محصول 09 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details10">' +
      '<a href="product-details-10.html">جزئیات محصول 10 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-details11">' +
      '<a href="product-details-11.html">جزئیات محصول 11 </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">علاقه‌مندی‌ها</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-favorite">' +
      '<a href="favorite.html">علاقه‌مندی 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-favorite2">' +
      '<a href="favorite-02.html">علاقه‌مندی 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-favorite3">' +
      '<a href="favorite-03.html">علاقه‌مندی 03 </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">نزدیک‌ترین‌ها</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-nearby">' +
      '<a href="nearby.html"> نزدیک‌ترین‌ها 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-nearby2">' +
      '<a href="nearby-02.html">نزدیک‌ترین‌ها 02 </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">پروفایل</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-profile">' +
      '<a href="profile.html">پروفایل 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-profile2">' +
      '<a href="profile-02.html">پروفایل 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-profile3">' +
      '<a href="profile-03.html">پروفایل 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-delivery">' +
      '<a href="location.html">مکان تحویل </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      '<li class="menu-item menu-item-has-children-mobile">' +
      '<a class="item-menu">سفارش</a>' +
      '<ul class="sub-menu-mobile">' +
      '<li class="menu-item-mobile current-payment">' +
      '<a href="payment.html">پرداخت 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-payment2">' +
      '<a href="payment-method.html">پرداخت 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-payment3">' +
      '<a href="payment-method-2.html">پرداخت 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-rate">' +
      '<a href="rate-driver.html">امتیازدهی</a>' +
      "</li>" +
      '<li class="menu-item-mobile current-track">' +
      '<a href="order-track.html">پیگیری سفارش 01 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-track2">' +
      '<a href="order-track-02.html">پیگیری سفارش 02 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-track3">' +
      '<a href="order-track-03.html">پیگیری سفارش 03 </a>' +
      "</li>" +
      '<li class="menu-item-mobile current-track4">' +
      '<a href="order-track-04.html">پیگیری سفارش 04 </a>' +
      "</li>" +
      "</ul>" +
      "</li>" +
      "</ul>" +
      "</div>" +
      "</div>" +
      "</div>";
    $("body").append(e);
  
    switchAnimate.btnmenu();
  });
  

var switchAnimate = {
  btnmenu: function () {
    $(document).on("click", ".menu-item-has-children-mobile", function () {
      var args = { duration: 600 };
      if ($(this).hasClass("active")) {
        $(this).children(".sub-menu-mobile").slideUp(args);
        $(this).removeClass("active");
      } else {
        $(".sub-menu-mobile").slideUp(args);
        $(this).children(".sub-menu-mobile").slideDown(args);
        $(".menu-item-has-children-mobile").removeClass("active");
        $(this).addClass("active");
      }
    });
  },
};
