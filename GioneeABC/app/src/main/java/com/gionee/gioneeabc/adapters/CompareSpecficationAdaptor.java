package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.CompareSpecficationBean;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by admin on 01-12-2016.
 */
public class CompareSpecficationAdaptor extends RecyclerView.Adapter<CompareSpecficationAdaptor.MyViewHolder> {


    private List<CompareSpecficationBean.CompareSubcategory> subcategories;
    Activity activity;

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView catname, type, gionne, nongionee;
        View spaceView;


        public MyViewHolder(View view) {
            super(view);
            catname = (TextView) view.findViewById(R.id.tvcat);
            type = (TextView) view.findViewById(R.id.tvtype);
            gionne = (TextView) view.findViewById(R.id.tvgionee);
            nongionee = (TextView) view.findViewById(R.id.tvother);
            spaceView = view.findViewById(R.id.space_view);
        }
    }


    public CompareSpecficationAdaptor(ArrayList<CompareSpecficationBean.CompareSubcategory> data, Activity activity) {
        subcategories = data;
        this.activity = activity;
    }



    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.compare_list_item, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        CompareSpecficationBean.CompareSubcategory data = subcategories.get(position);

        if (data.getCatName() != null && !data.getCatName().equalsIgnoreCase("")) {
            holder.catname.setVisibility(View.VISIBLE);
            holder.catname.setText(data.getCatName());
            holder.spaceView.setVisibility(View.VISIBLE);
        } else {
            holder.catname.setVisibility(View.GONE);
            holder.spaceView.setVisibility(View.GONE);
        }

        holder.type.setText(data.getSubcatName());
        if (data.getGionee() != null && !data.getGionee().equals(""))
            holder.gionne.setText(data.getGionee());
        if (data.getOther() != null && !data.getOther().equals(""))
            holder.nongionee.setText(data.getOther());

    }


    @Override
    public int getItemCount() {
        return subcategories.size();
    }


}