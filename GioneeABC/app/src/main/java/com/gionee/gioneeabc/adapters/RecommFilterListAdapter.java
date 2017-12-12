package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.content.Intent;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.helpers.UIUtils;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Created by Linchpin
 */
public class RecommFilterListAdapter extends RecyclerView.Adapter<RecommFilterListAdapter.ViewHolder> {
    private Activity activity;
    private List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList;
    private String type;
    private int brandNamePos;
    private Map<Integer, RecommNonGioneeModelBean.Model> selModelList=new HashMap<>();
    private RecommAttribBean.RecommAttribData recommAttribData;
    private List<String> selSearchAttrib=new ArrayList<>();

    public RecommFilterListAdapter(Activity activity, List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList,
                                   String type, int brandNamePos, RecommAttribBean.RecommAttribData recommAttribData) {
        this.activity = activity;
        this.brandNameList = brandNameList;
        this.type = type;
        this.brandNamePos = brandNamePos;
        this.recommAttribData=recommAttribData;

        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)){
            if (recommAttribData.getSelSearchAttrib()!=null) {
                selSearchAttrib=recommAttribData.getSelSearchAttrib();
            }
        }
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.row_filter_list, parent, false);
        return new ViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND)) {
            RecommNonGioneeModelBean.RecommNonGioneeModeData recommNonGioneeModeData=brandNameList.get(position);
            holder.tvTitle.setText(recommNonGioneeModeData.getName());
        } else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL)) {
            RecommNonGioneeModelBean.Model model=brandNameList.get(brandNamePos).getModel().get(position);
            holder.tvTitle.setText(model.getModelName());
            if (selModelList.size()>0) {
                if (selModelList.containsKey(position)){
                    holder.ivCheckbox.setTag(activity.getString(R.string.tag_check));
                    holder.ivCheckbox.setImageResource(R.drawable.ic_check_box);
                }else {
                    holder.ivCheckbox.setTag(activity.getString(R.string.tag_uncheck));
                    holder.ivCheckbox.setImageResource(R.drawable.ic_check_box_outline_blank);
                }
            }else {
                holder.ivCheckbox.setTag(activity.getString(R.string.tag_uncheck));
                holder.ivCheckbox.setImageResource(R.drawable.ic_check_box_outline_blank);
            }
        }else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)){
            String attrib=recommAttribData.getSearch_attribute().get(position);
            holder.tvTitle.setText(attrib);
            if (recommAttribData.getSelSearchAttrib()!=null) {
                if (recommAttribData.getSelSearchAttrib().contains(attrib)) {
                    holder.ivCheckbox.setTag(activity.getString(R.string.tag_check));
                    holder.ivCheckbox.setImageResource(R.drawable.ic_check_box);
                } else {
                    holder.ivCheckbox.setTag(activity.getString(R.string.tag_uncheck));
                    holder.ivCheckbox.setImageResource(R.drawable.ic_check_box_outline_blank);
                }
            }
        }
    }

    @Override
    public int getItemCount() {
        int size = 0;
        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND))
            size = brandNameList.size();
        else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL))
            size = brandNameList.get(brandNamePos).getModel().size();
        else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB))
            size=recommAttribData.getSearch_attribute().size();
        return size;
    }

    public class ViewHolder extends RecyclerView.ViewHolder {
        private TextView tvTitle;
        private ImageView ivCheckbox;

        public ViewHolder(View itemView) {
            super(itemView);
            itemView.setClickable(true);
            tvTitle = (TextView) itemView.findViewById(R.id.tv_title);
            ivCheckbox = (ImageView) itemView.findViewById(R.id.iv_checkbox);

            if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND)) {
                ivCheckbox.setVisibility(View.GONE);
            } else {
                ivCheckbox.setVisibility(View.VISIBLE);
            }
            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND)) {
                        Intent intent = new Intent();
                        intent.putExtra("name", brandNameList.get(getPosition()).getName());
                        intent.putExtra("id", brandNameList.get(getPosition()).getId());
                        intent.putExtra("brand_name_pos", getPosition());
                        activity.setResult(101, intent);
                        activity.finish();
                    }else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL)){
                        if (ivCheckbox.getTag().toString().equalsIgnoreCase(activity.getString(R.string.tag_uncheck))){
                            selModelList.put(getPosition(), brandNameList.get(brandNamePos).getModel().get(getPosition()));
                            ivCheckbox.setTag(activity.getString(R.string.tag_check));
                            ivCheckbox.setImageResource(R.drawable.ic_check_box);
                        }else {
                            selModelList.remove(getPosition());
                            ivCheckbox.setTag(activity.getString(R.string.tag_uncheck));
                            ivCheckbox.setImageResource(R.drawable.ic_check_box_outline_blank);
                        }
                    }else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)){
                        if (ivCheckbox.getTag().toString().equalsIgnoreCase(activity.getString(R.string.tag_uncheck))){
                            selSearchAttrib.add(recommAttribData.getSearch_attribute().get(getPosition()));
                            recommAttribData.setSelSearchAttrib(selSearchAttrib);
                            ivCheckbox.setTag(activity.getString(R.string.tag_check));
                            ivCheckbox.setImageResource(R.drawable.ic_check_box);
                        }else {
                            selSearchAttrib.remove(recommAttribData.getSearch_attribute().get(getPosition()));
                            recommAttribData.setSelSearchAttrib(selSearchAttrib);
                            ivCheckbox.setTag(activity.getString(R.string.tag_uncheck));
                            ivCheckbox.setImageResource(R.drawable.ic_check_box_outline_blank);
                        }
                    }
                }
            });
        }
    }

    public Map<Integer, RecommNonGioneeModelBean.Model> getSelModelList() {
        return selModelList;
    }

    public void setSelModelList(Map<Integer, RecommNonGioneeModelBean.Model> selModelList) {
        this.selModelList = selModelList;
    }

    public RecommAttribBean.RecommAttribData getRecommAttribData() {
        return recommAttribData;
    }

    public void setRecommAttribData(RecommAttribBean.RecommAttribData recommAttribData) {
        this.recommAttribData = recommAttribData;
    }
}
