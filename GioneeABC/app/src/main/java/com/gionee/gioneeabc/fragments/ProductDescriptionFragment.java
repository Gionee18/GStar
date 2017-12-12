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

/**
 * Created by Linchpin25 on 4/8/2016.
 */
public class ProductDescriptionFragment extends Fragment
{
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
        settings.setDefaultTextEncodingName("utf-8");

        wvSpecification.loadDataWithBaseURL(null, ((ProductDetailActivity) getActivity()).product.getProductDesc(), "text/html", "UTF-8", null);

    }



}
