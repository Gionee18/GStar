package com.gionee.gioneeabc.fragments;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebSettings;
import android.webkit.WebView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ProductDetailActivity;
import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class ProductSpecificationFragment extends Fragment {
    View rootView;
    WebView wvSpecification;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        rootView = inflater.inflate(R.layout.product_specification_fragment, null);
        initView();
        return rootView;
    }

    private void initView() {

        wvSpecification = (WebView) rootView.findViewById(R.id.wvSpecification);
        WebSettings settings = wvSpecification.getSettings();
    /*    settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);*/
        settings.setDefaultTextEncodingName("utf-8");
        wvSpecification.loadDataWithBaseURL(null, Util.setFontInText(((ProductDetailActivity) getActivity()).product.getProductDesc1()), "text/html", "UTF-8", null);


     /*   wvSpecification.setText(Html.fromHtml(((ProductDetailActivity) getActivity()).product.getProductDesc1()));
        Typeface face = Typeface.createFromAsset(getActivity().getAssets(), "font/spe_font.otf");
        wvSpecification.setTypeface(face);*/
    }



}
