package com.gionee.gioneeabc.fragments;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.ListView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ProductDetailActivity;
import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class ProductOverviewFragment extends Fragment {
    View rootView;


    ListView list;
    RecyclerView rvCompetitionDetail;
    WebView wvSpecification;


    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        rootView = inflater.inflate(R.layout.product_specification_fragment, container, false);
        initView();
        return rootView;
    }


    private void initView() {
        wvSpecification = (WebView) rootView.findViewById(R.id.wvSpecification);
        wvSpecification.setVerticalScrollBarEnabled(true);
        WebSettings settings = wvSpecification.getSettings();
        settings.setDefaultTextEncodingName("utf-8");
      /*  settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);*/
        wvSpecification.loadDataWithBaseURL(null, Util.setFontInText(((ProductDetailActivity) getActivity()).product.getProductDesc()), "text/html", "UTF-8", null);

       /* wvSpecification.setText(Html.fromHtml(((ProductDetailActivity) getActivity()).product.getProductDesc1()));
        Typeface face = Typeface.createFromAsset(getActivity().getAssets(), "font/spe_font.otf");
        wvSpecification.setTypeface(face);*/
    }

}
