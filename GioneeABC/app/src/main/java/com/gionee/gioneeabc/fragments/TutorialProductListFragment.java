package com.gionee.gioneeabc.fragments;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.widget.NestedScrollView;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.TutorialProductsActivity;
import com.gionee.gioneeabc.adapters.TutorialProductListAdapter;
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.helpers.Util;

import java.util.ArrayList;

/**
 * Created by root on 4/10/16.
 */
public class TutorialProductListFragment extends Fragment {
    private View root;
    private RecyclerView recyclerView;
    private NestedScrollView nestedScrollView;
    private TextView tvNoProducts;
    private RecyclerView.Adapter adapter;
    private TutorialResponseBean tutorialResponseBean;
    private ArrayList<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> productBeen;
    public int tutorialPageSelected;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        root = inflater.inflate(R.layout.tutorial_product_list_fragment, null);
        initView();
        fetchData();
        return root;
    }

    private void initView() {
        nestedScrollView = (NestedScrollView) root.findViewById(R.id.nestedScrollView);
        recyclerView = (RecyclerView) root.findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        recyclerView.setVisibility(View.GONE);
        tvNoProducts = (TextView) root.findViewById(R.id.tvNoProducts);
        tvNoProducts.setTypeface(Util.getRoboMedium(getActivity()));
    }


    public void fetchData() {
        productBeen = new ArrayList<>();
        tutorialPageSelected = TutorialProductsActivity.selectedPage;
        this.tutorialResponseBean = TutorialProductsActivity.tutorialDataCatogaryBean;
        for (int i = 0; i < tutorialResponseBean.getData().get(tutorialPageSelected).getProduct().size(); i++) {
            productBeen.add(tutorialResponseBean.getData().get(tutorialPageSelected).getProduct().get(i));
        }

        if (productBeen == null) {
            recyclerView.setVisibility(View.GONE);
            tvNoProducts.setVisibility(View.VISIBLE);
            nestedScrollView.setVisibility(View.VISIBLE);
        } else {
            recyclerView.setVisibility(View.VISIBLE);
            tvNoProducts.setVisibility(View.GONE);
            nestedScrollView.setVisibility(View.GONE);

            if (productBeen != null && productBeen.size() > 0)
                adapter = new TutorialProductListAdapter(getActivity(), productBeen);
            recyclerView.setAdapter(adapter);
        }
    }
}
