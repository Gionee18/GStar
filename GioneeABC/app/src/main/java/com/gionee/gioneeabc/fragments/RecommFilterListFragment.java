package com.gionee.gioneeabc.fragments;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.RecommFilterListAdapter;

/**
 * A simple {@link Fragment} subclass.
 */
public class RecommFilterListFragment extends UtilityFragment {
    private TextView tvApply, tvClear;
    private RecyclerView recyclerView;
    private RecommFilterListAdapter adapter;

    @Override
    public int getLayoutResId() {
        return R.layout.fragment_recomm_filter_list;
    }

    @Override
    public void initializeViews(Bundle savedInstanceState) {
        tvApply = (TextView) fView.findViewById(R.id.tv_apply);
        tvClear = (TextView) fView.findViewById(R.id.tv_clear);
        recyclerView=(RecyclerView)fView.findViewById(R.id.recyclerView);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
    }

    @Override
    public void setDataOnViews(Bundle savedInstanceState) {

    }

    @Override
    public void handleViewsVisibility(Bundle savedInstanceState) {

    }

    @Override
    public void setListenersOnViews(Bundle savedInstanceState) {

    }
}
