package com.gionee.gioneeabc.helpers;

import com.gionee.gioneeabc.bean.CompareSpecficationBean;

/**
 * Created by Linchpin
 */
public class UIUtils {
    public static final String TAG_RECOMMENDER_FRAGMENT = "RecommenderFragemnt";
    public static final String RECOMM_KEY_TYPE = "type_filter";
    public static final String RECOMM_VALUE_BRAND = "brand";
    public static final String RECOMM_VALUE_MODEL = "model";
    public static final String RECOMM_KEY_BRAND_NAME_POS = "brand_name_pos";
    public static final String RECOMM_KEY_SEL_MODEL = "sel_model_list";
    public static final String RECOMM_VALUE_ATTRIB = "recomm_attrib";
    public static final String RECOMM_KEY_SEL_ATTRIB = "sel_recomm_attrib";
    public static final String RECOMM_KEY_BRAND_MODEL_BEAN = "brand_model_bean";
    public static final String RECOMM_KEY_FROM = "from";
    public static final String RECOMM_FROM_VALUE_FILTER = "filter";
    public static final String RECOMM_FROM_VALUE_MAIN = "main";
    public static final String RECOMM_KEY_FILTER_TYPE = "type";
    public static final String RECOMM_VALUE_FILTER_MANUFACTURER = "manufacturer";
    public static final String RECOMM_VALUE_FILTER_ATTRIB = "attributes";

    public static boolean isFilterFromProduct=false;
    public static CompareSpecficationBean compareSpecficationBean;
    public static int selectedGioneeModel = -1, selectedNonGioneeModel = -1;
}
