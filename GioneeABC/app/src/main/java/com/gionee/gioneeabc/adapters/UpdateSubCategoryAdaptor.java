package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.content.Intent;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.TopicActivity;
import com.gionee.gioneeabc.bean.UpdateResponseBean;
import com.gionee.gioneeabc.helpers.CircularTextView;

import java.util.List;

/**
 * Created by admin on 01-12-2016.
 */
public class UpdateSubCategoryAdaptor extends RecyclerView.Adapter<UpdateSubCategoryAdaptor.MyViewHolder> {


    private List<UpdateResponseBean.Subcategory> subcategories;
    Activity activity;

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView name;
        public CircularTextView tvNew;


        public MyViewHolder(View view) {
            super(view);
            name = (TextView) view.findViewById(R.id.name);
            tvNew = (CircularTextView) view.findViewById(R.id.tv_new);
        }
    }


    public UpdateSubCategoryAdaptor(List<UpdateResponseBean.Subcategory> data, Activity activity) {
        subcategories = data;
        this.activity = activity;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.update_cat_item, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        UpdateResponseBean.Subcategory data = subcategories.get(position);

        holder.name.setText(data.getSubcategory_name());
        if (data.getUnreadCount() > 0) {
            holder.tvNew.setVisibility(View.VISIBLE);
            holder.tvNew.setText(data.getUnreadCount() + "");
        } else {
            holder.tvNew.setVisibility(View.GONE);
        }

        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(activity, TopicActivity.class);
                i.putExtra("data", subcategories.get(position));
                activity.startActivityForResult(i, 1);
            }
        });

    }


    @Override
    public int getItemCount() {
        return subcategories.size();
    }


}