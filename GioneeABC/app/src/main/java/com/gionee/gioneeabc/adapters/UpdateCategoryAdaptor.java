package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.content.Intent;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.UpdateDetailActivity;
import com.gionee.gioneeabc.bean.UpdateResponseBean;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.interfaces.OnLoadMoreListener;

import java.util.List;

/**
 * Created by admin on 01-12-2016.
 */
public class UpdateCategoryAdaptor extends RecyclerView.Adapter<UpdateCategoryAdaptor.MyViewHolder> {

    private static final int SET_READ = 101;
    private List<UpdateResponseBean.Topic> topicList;
    Activity activity;
    private OnLoadMoreListener mOnLoadMoreListener;

    private boolean isLoading=true;

    private int lastVisibleItem, totalItemCount;
    private boolean isFirstTime = true;

    private String searchString;

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView name;


        public MyViewHolder(View view) {
            super(view);
            name = (TextView) view.findViewById(R.id.name);
        }
    }


    public UpdateCategoryAdaptor(List<UpdateResponseBean.Topic> data, Activity activity, RecyclerView mRecyclerView) {
        topicList = data;
        this.activity = activity;
        final LinearLayoutManager linearLayoutManager = (LinearLayoutManager) mRecyclerView.getLayoutManager();
        mRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                super.onScrolled(recyclerView, dx, dy);

                if (!isFirstTime) {
                    totalItemCount = linearLayoutManager.getItemCount();
                    lastVisibleItem = linearLayoutManager.findLastVisibleItemPosition();

                    if (!isLoading && totalItemCount <= (lastVisibleItem + NetworkConstants.visibleThreshold)) {
                        if (mOnLoadMoreListener != null) {
                            mOnLoadMoreListener.onLoadMore();
                        }
                        isLoading = true;
                    }
                }
                isFirstTime = false;
            }
        });
    }

    public void setOnLoadMoreListener(OnLoadMoreListener mOnLoadMoreListener) {
        this.mOnLoadMoreListener = mOnLoadMoreListener;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.topic_item, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        UpdateResponseBean.Topic data = topicList.get(position);
        if (data.getIsRead() == 0) {
            holder.name.setText(Html.fromHtml("<b>" + data.getTopicName() + "</b>"));
        } else
            holder.name.setText(data.getTopicName());


        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(activity, UpdateDetailActivity.class);
                i.putExtra("data", topicList.get(position));
                activity.startActivityForResult(i, 1);
            }
        });

    }

    public void setSearchString(String search) {
        searchString = search;
    }


    @Override
    public int getItemCount() {
        return topicList.size();
    }

    public void setLoaded() {
        isLoading = false;
    }
}