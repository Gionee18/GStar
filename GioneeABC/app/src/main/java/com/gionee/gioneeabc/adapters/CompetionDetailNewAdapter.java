package com.gionee.gioneeabc.adapters;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;

/**
 * Created by Linchpin25 on 12/18/2015.
 */
public class CompetionDetailNewAdapter extends RecyclerView.Adapter<CompetionDetailNewAdapter.CustomViewHolder> {

    String[] fruitsList;
    Context context;
    View rootView;

    public CompetionDetailNewAdapter(String[] fruitsList, Context context) {
        this.context = context;
        this.fruitsList = fruitsList;
    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {
        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.competition_detail_row, null);

        CustomViewHolder viewHolder = new CustomViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, int position) {
        holder.tvHeader.setText(fruitsList[position]);

    }

    @Override
    public int getItemCount() {
        return fruitsList.length;
    }

    public class CustomViewHolder extends RecyclerView.ViewHolder {

        TextView tvHeader;


        public CustomViewHolder(View itemView) {
            super(itemView);
            tvHeader = (TextView) itemView.findViewById(R.id.tv_header);

        }


    }


}
