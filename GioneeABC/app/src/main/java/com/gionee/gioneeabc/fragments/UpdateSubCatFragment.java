package com.gionee.gioneeabc.fragments;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.UpdateActivity;
import com.gionee.gioneeabc.adapters.UpdateSubCategoryAdaptor;
import com.gionee.gioneeabc.bean.UpdateResponseBean;

import java.util.ArrayList;
import java.util.List;

/**
 * A simple {@link Fragment} subclass.
 */
public class UpdateSubCatFragment extends Fragment {

    private RecyclerView recyclerView;
    List<UpdateResponseBean.Subcategory> subcategory = new ArrayList<>();
    private UpdateSubCategoryAdaptor adaptor;

    private View view;
    private int position;
    TextView tvNoResult;

//    public UpdateSubCatFragment() {
//    }
//
//    public UpdateSubCatFragment(int position) {
//        this.position = position;
//    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.fragment_update_sub_cat, null);

        position = getArguments().getInt("position");
        init();

        return view;
    }

    private void init() {

        tvNoResult = (TextView) view.findViewById(R.id.no_result);
        subcategory = ((UpdateActivity) getActivity()).updateResponseBean.getData().get(position).getSubcategory();
        if (subcategory != null && subcategory.size() > 0) {

            for (int i = 0; i < subcategory.size(); i++) {
                if (subcategory.get(i).getId() == 0) {
                    UpdateResponseBean.Subcategory sub = subcategory.get(i);
                    subcategory.remove(i);
                    subcategory.add(sub);
                    break;
                }
            }
            recyclerView = (RecyclerView) view.findViewById(R.id.recyclerView);
            adaptor = new UpdateSubCategoryAdaptor(subcategory, getActivity());
            recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
            recyclerView.setAdapter(adaptor);
            tvNoResult.setVisibility(View.GONE);
        } else
            tvNoResult.setVisibility(View.VISIBLE);


    }


    public void refresh() {
        if (adaptor != null)
            adaptor.notifyDataSetChanged();
    }

}
