package com.gionee.gioneeabc.helpers;

/**
 * Created by Linchpin25 on 2/25/2016.
 */

public class NetworkConstants {
    //    public static final String BASE_URL = "http://gioneestaging.tk/star/services/";
//      public static final String BASE_URL = "http://192.168.11.119/gstar/services/";
    public static final String BASE_URL = "http://gstar.gionee.co.in/services_new/";
//    public static final String BASE_URL = "http://103.20.213.33/gionee_star/services_new/";           //client staging url
//    public static final String BASE_URL = "http://103.20.213.33/gstar/services/";             //our testing staging url


    //kapil@lptpl.com       lptpl.


    public static final String LOGIN_URL = BASE_URL + "oauth/access_token";
    public static final String USER_INFO_URL = BASE_URL + "v1/user/data?access_token=";
    public static final String USER_ACTIVATION_REQUEST = BASE_URL + "v1/user/activation/request";
    public static final String LOGOUT_URL = BASE_URL + "v1/app/logout";
    public static final String GET_CATEGORY_URL = BASE_URL + "categories";
    public static final String GET_STATE_LIST = BASE_URL + "v1/address/state";
    public static final String GET_CITY_LIST = BASE_URL + "v1/address/city";
    public static final String GET_DASHBOARD_IMAGES_URL = BASE_URL + "v1/assets/homeBannerImages?access_token=";
    public static final String GET_PRODUCTS_BY_CATEGORY_URL = BASE_URL + "v1/category/product";
    public static final String GET_PRODUCTS_DETAIL_URL = BASE_URL + "v1/product/details";
    public static final String FORGOT_PASSWORD_URL = BASE_URL + "user/forgetPassword";
    public static final String CHANGE_PASSWORD_URL = BASE_URL + "v1/user/changeAppPassword";
    public static final String USER_DATA = BASE_URL + "v1/appUser/edit/";
    public static final String SET_USER_DATA = BASE_URL + "v1/appUser/edit";
    public static final String UPDATE_URL = BASE_URL + "v1/app/update";
    public static final String UPDATE_COUNT_URL = BASE_URL + "v1/app/updateCount";
    public static final String GET_NEW_PRODUCTS = BASE_URL + "v1/productDetails";  //v1/productDetails?access_token=V69cgHN6I1nDEhsiH4L08qMDjB0oZEbmb3QLfXV5
    public static final String hideImageFromGallery = "";
    public static final String hideFolderFromGallery = "/.";
    public static final String GETNDLIST = BASE_URL + "v1/user/nd/list";
    public static final String GETRDSLIST = BASE_URL + "v1/user/rd/list";
    public static final String GETZONELIST = BASE_URL + "v1/address/zone";
    public static final String GET_TUTORIALS = BASE_URL + "v1/app/list/tutorial";
    public static final String GET_MORE_TOPIC = BASE_URL + "v1/news/update/topics";
    public static final String CHECK_VERSION = BASE_URL + "version";
    public static final String CATEGORY_UPDATE_URL = BASE_URL + "v1/news/update/list";
    public static final String SET_CATEGORY_READ_URL = BASE_URL + "v1/news/update/readstatus";
    public static final String DEVICE_REGISTETR = BASE_URL + "v1/savedevice";
    public static final String GET_RECOMM_MODEL_DATA = BASE_URL + "v1/app/search/mf/model/list";
    public static final String GET_RECOMM_ATTRIB_DATA = BASE_URL + "v1/app/search/attribute/list";
    public static final String GET_RECOMM_FILTER_PRODUCT_LIST = BASE_URL + "v1/app/search/recommendor";
    public static final String PHONE_SPECIFICATION_COMP = BASE_URL + "v1/app/phone/compair";
    public static final String AUDIT_TRAIL = BASE_URL + "v1/save/user/trail";

    public static final int PAGE_REFRESH_TIME = 200;
    public static final int visibleThreshold = 20;
}
