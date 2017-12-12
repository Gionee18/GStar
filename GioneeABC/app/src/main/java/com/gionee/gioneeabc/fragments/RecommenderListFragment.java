package com.gionee.gioneeabc.fragments;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.RecomenderActivity;
import com.gionee.gioneeabc.activities.RecommFilterListActivity;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;
import com.google.gson.Gson;
import com.yahoo.mobile.client.android.util.rangeseekbar.RangeSeekBar;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Created by root on 2/11/16.
 */
public class RecommenderListFragment extends Fragment implements View.OnClickListener {
    private View root;
    private boolean isManufacturer = true;
    private RelativeLayout rlBrand, rlModel;
    private TextView tvBrand, tvModel, tvClear, tvApply, tvMoreFilter, tvMinPrice, tvMaxPrice;
    private LinearLayout llFilterCateg;
    private RangeSeekBar<Integer> crystalRangeSeekbar;

    private int selBrandNamePos;
    private Map<Integer, RecommNonGioneeModelBean.Model> selModelList = new HashMap<>();
    private RecommAttribBean recommAttribBean;
    private int selBrandId;
    private RecommNonGioneeModelBean recommNonGioneeModelBean;
    private int selMinPrice = -1, selMaxPrice = -1;
    private TextView tvDisclaimer;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        root = inflater.inflate(R.layout.recommender_list_fragment, null);
        initView();
        Bundle bundle = getArguments();
        if (bundle != null) {
            if (bundle.getString(UIUtils.RECOMM_KEY_FILTER_TYPE) != null) {
                String type = bundle.getString(UIUtils.RECOMM_KEY_FILTER_TYPE);
                if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER)) {
                    isManufacturer = true;
                } else {
                    isManufacturer = false;
                }
            }
        }
        if (isManufacturer) {
            rlBrand.setVisibility(View.VISIBLE);
            rlModel.setVisibility(View.GONE);
            setSelectedBrandModel();
        } else {
            rlBrand.setVisibility(View.GONE);
            rlModel.setVisibility(View.GONE);
            tvDisclaimer.setVisibility(View.GONE);
        }
        initListener();
        setAttributes();
        return root;
    }

    private void initView() {
        rlBrand = (RelativeLayout) root.findViewById(R.id.rl_brand);
        rlModel = (RelativeLayout) root.findViewById(R.id.rl_model);
        tvBrand = (TextView) root.findViewById(R.id.tv_brand);
        tvModel = (TextView) root.findViewById(R.id.tv_model);
        tvMoreFilter = (TextView) root.findViewById(R.id.tv_more_filter);
        tvApply = (TextView) root.findViewById(R.id.tv_apply);
        tvClear = (TextView) root.findViewById(R.id.tv_clear);
        tvMinPrice = (TextView) root.findViewById(R.id.tv_min_price);
        tvMaxPrice = (TextView) root.findViewById(R.id.tv_max_price);
        llFilterCateg = (LinearLayout) root.findViewById(R.id.ll_filter_categories);
        crystalRangeSeekbar = (RangeSeekBar<Integer>) root.findViewById(R.id.seek_bar);

        tvDisclaimer = (TextView) root.findViewById(R.id.disclaimer);
        tvDisclaimer.setText(DataStore.getDisclaimer(getContext()));
    }

    private void setSelectedBrandModel() {
        rlBrand.setVisibility(View.VISIBLE);
        tvBrand.setText(getString(R.string.text_select_brand));
        tvBrand.setTextColor(getResources().getColor(R.color.half_black));
        rlModel.setVisibility(View.GONE);
        Cursor cursor = DataBaseHandler.getInstance(getActivity()).getAllRecommModelData();
        if (cursor.moveToFirst()) {
            String modelResponse = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            recommNonGioneeModelBean = new Gson().fromJson(modelResponse, RecommNonGioneeModelBean.class);
            if (recommNonGioneeModelBean.getStatus().equalsIgnoreCase("success")) {
                List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean.getData();
                if (brandNameList.size() > 0) {
                    for (int i = 0; i < brandNameList.size(); i++) {
                        if (brandNameList.get(i).isSelected()) {
                            selBrandId = brandNameList.get(i).getId();
                            selBrandNamePos = i;
                            tvBrand.setText(brandNameList.get(i).getName());
                            tvBrand.setTextColor(getResources().getColor(R.color.colorPrimary));
                            rlModel.setVisibility(View.VISIBLE);
                            List<RecommNonGioneeModelBean.Model> modelList = brandNameList.get(i).getModel();
                            if (modelList.size() > 0) {
                                List<RecommNonGioneeModelBean.Model> modelArrayList = new ArrayList<>();
                                selModelList.clear();
                                for (int j = 0; j < modelList.size(); j++) {
                                    if (modelList.get(j).isSelected()) {
                                        modelArrayList.add(modelList.get(j));
                                        selModelList.put(j, modelList.get(j));
                                    }
                                }
                                setSelectedModelNames(modelArrayList);
                            }
                            break;
                        }
                    }
                }
            }
        }
    }

    private void setSelectedModelNames(List<RecommNonGioneeModelBean.Model> modelArrayList) {
        StringBuilder stringBuilder = new StringBuilder();
        for (int i = 0; i < modelArrayList.size(); i++) {
            if (i > 1) {
                stringBuilder.append(" & " + (modelArrayList.size() - 2) + " more");
                break;
            } else {
                if (i == 1) {
                    stringBuilder.append(modelArrayList.get(i).getModelName());
                } else {
                    if (i != (modelArrayList.size() - 1)) {
                        stringBuilder.append(modelArrayList.get(i).getModelName() + ", ");
                    } else {
                        stringBuilder.append(modelArrayList.get(i).getModelName());
                    }
                }
            }
        }
        tvModel.setText(stringBuilder.toString());
        tvModel.setTextColor(getResources().getColor(R.color.colorPrimary));
    }

    private void initListener() {
        rlBrand.setOnClickListener(this);
        rlModel.setOnClickListener(this);
        tvMoreFilter.setOnClickListener(this);
        tvApply.setOnClickListener(this);
        tvClear.setOnClickListener(this);
    }

    private void setSeekBarListener() {
        /*crystalRangeSeekbar.setOnRangeSeekbarChangeListener(new OnRangeSeekbarChangeListener() {
            @Override
            public void valueChanged(Number minValue, Number maxValue) {
                tvMinPrice.setText(String.valueOf(minValue));
                tvMaxPrice.setText(String.valueOf(maxValue));
                selMinPrice = minValue.intValue();
                selMaxPrice = maxValue.intValue();
            }
        });

        crystalRangeSeekbar.setOnRangeSeekbarFinalValueListener(new OnRangeSeekbarFinalValueListener() {
            @Override
            public void finalValue(Number minValue, Number maxValue) {
                Log.d("CRS=>", String.valueOf(minValue) + " : " + String.valueOf(maxValue));
            }
        });*/
        crystalRangeSeekbar.setOnRangeSeekBarChangeListener(new RangeSeekBar.OnRangeSeekBarChangeListener<Integer>() {
            @Override
            public void onRangeSeekBarValuesChanged(RangeSeekBar<?> bar, Integer minValue, Integer maxValue) {
                tvMinPrice.setText(String.valueOf(minValue));
                tvMaxPrice.setText(String.valueOf(maxValue));
                selMinPrice = minValue.intValue();
                selMaxPrice = maxValue.intValue();
            }
        });
    }

    @Override
    public void onClick(View v) {
        if (v == rlBrand) {
            Intent intent = new Intent(getActivity(), RecommFilterListActivity.class);
            intent.putExtra(UIUtils.RECOMM_KEY_TYPE, UIUtils.RECOMM_VALUE_BRAND);
            startActivityForResult(intent, 101);
        } else if (v == rlModel) {
            Intent intent = new Intent(getActivity(), RecommFilterListActivity.class);
            intent.putExtra(UIUtils.RECOMM_KEY_TYPE, UIUtils.RECOMM_VALUE_MODEL);
            intent.putExtra(UIUtils.RECOMM_KEY_BRAND_NAME_POS, selBrandNamePos);
            intent.putExtra(UIUtils.RECOMM_KEY_SEL_MODEL, (Serializable) selModelList);
            startActivityForResult(intent, 102);
        } else if (v == tvMoreFilter) {

        } else if (v == tvApply) {
            if (isManufacturer) {
                if (tvBrand.getText().toString().equalsIgnoreCase(getString(R.string.text_select_brand))) {
                    Util.createToast(getActivity(), getString(R.string.msg_please_select_brand));
                    return;
                } /*else if (tvModel.getText().toString().equalsIgnoreCase(getString(R.string.text_select_model))) {
                    Util.createToast(getActivity(), getString(R.string.msg_please_select_model));
                    return;
                }*/
            }
            for (int k = recommAttribBean.getData().size() - 1; k >= 0; k--) {
                if (recommAttribBean.getData().get(k).getName().equalsIgnoreCase("price")) {
                    String s = "";
                    if (selMinPrice != -1)
                        s = "" + selMinPrice;
                    if (selMaxPrice != -1) {
                        if (s.isEmpty())
                            s = "0 -" + selMaxPrice;
                        else
                            s = s + " -" + selMaxPrice;
                    }
                    if (!s.isEmpty()) {
                        List<String> list = new ArrayList<>();
                        list.add(s);
                        recommAttribBean.getData().get(k).setSelSearchAttrib(list);
                    }
                    break;
                }
            }
            DataBaseHandler.getInstance(getActivity()).deleteAllRecommAttribData();
            DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommAttribBean), DataBaseHandler.TYPE_RECOMM_ATTRIB);
            if (!tvBrand.getText().toString().equalsIgnoreCase(getString(R.string.text_select_brand))) {
                ArrayList<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean.getData();
                if (brandNameList.size() > 0) {
                    for (int i = 0; i < brandNameList.size(); i++) {
                        if (brandNameList.get(i).getId() == selBrandId) {
                            brandNameList.get(i).setIsSelected(true);
                            if (selModelList.size() > 0) {
                                List<RecommNonGioneeModelBean.Model> modelList = brandNameList.get(i).getModel();
                                if (modelList.size() > 0) {
                                    for (int j = 0; j < modelList.size(); j++) {
                                        if (selModelList.containsKey(j)) {
                                            modelList.get(j).setIsSelected(true);
                                        }
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
                recommNonGioneeModelBean.setData(brandNameList);
                DataBaseHandler.getInstance(getActivity()).deleteAllRecommModelData();
                DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommNonGioneeModelBean), DataBaseHandler.TYPE_RECOMM_MODEL);
            }

            UIUtils.isFilterFromProduct = false;
            Intent intent = new Intent(getActivity(), RecomenderActivity.class);
            if (isManufacturer)
                intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
            else
                intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_ATTRIB);
            intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_FILTER);
            startActivity(intent);
            getActivity().finish();

        } else if (v == tvClear) {
            if (isManufacturer) {
                if (Util.isBrandModelSelected(getActivity()) || Util.isAttribSelected(getActivity())) {
                    openClearDialog();
                } else {
                    setSelectedBrandModel();
                    setAttributes();
//                    Util.createToast(getActivity(), getString(R.string.msg_no_filter_selected));
                }
            } else {
                if (Util.isAttribSelected(getActivity())) {
                    openClearDialog();
                } else {
                    setAttributes();
//                    Util.createToast(getActivity(), getString(R.string.msg_no_filter_selected));
                }
            }
        }
    }

    private void openClearDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());

        builder.setTitle("Clear Filters");
        builder.setMessage("Are you sure you want to clear the filters?");

        // Set the action buttons
        builder.setPositiveButton("CLEAR", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int id) {
                clearFilters();
            }
        });

        builder.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int id) {
                dialog.cancel();
            }
        });
        builder.show();
    }

    private void clearFilters() {
        if (isManufacturer) {
            if (!tvBrand.getText().toString().equalsIgnoreCase(getString(R.string.text_select_brand))) {
                ArrayList<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean.getData();
                if (brandNameList.size() > 0) {
                    for (int i = 0; i < brandNameList.size(); i++) {
                        if (brandNameList.get(i).getId() == selBrandId) {
                            brandNameList.get(i).setIsSelected(false);
                            if (selModelList.size() > 0) {
                                List<RecommNonGioneeModelBean.Model> modelList = brandNameList.get(i).getModel();
                                if (modelList.size() > 0) {
                                    for (int j = 0; j < modelList.size(); j++) {
                                        if (selModelList.containsKey(j)) {
                                            modelList.get(j).setIsSelected(false);
                                        }
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
                recommNonGioneeModelBean.setData(brandNameList);
                DataBaseHandler.getInstance(getActivity()).deleteAllRecommModelData();
                DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommNonGioneeModelBean), DataBaseHandler.TYPE_RECOMM_MODEL);
//                rlBrand.setVisibility(View.VISIBLE);
//                tvBrand.setText(getString(R.string.text_select_brand));
//                tvBrand.setTextColor(getResources().getColor(R.color.half_black));
//                rlModel.setVisibility(View.GONE);
                setSelectedBrandModel();
            }
        }
        for (int i = 0; i < recommAttribBean.getData().size(); i++) {
            if (recommAttribBean.getData().get(i).getSelSearchAttrib() != null)
                recommAttribBean.getData().get(i).getSelSearchAttrib().clear();
        }
        DataBaseHandler.getInstance(getActivity()).deleteAllRecommAttribData();
        DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommAttribBean), DataBaseHandler.TYPE_RECOMM_ATTRIB);
        setAttributes();

        if (((RecomenderActivity) getActivity()).getAdapter() != null)
            ((RecomenderActivity) getActivity()).getAdapter().notifyDataSetChanged();
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (data != null) {
            if (requestCode == 101) {
                ArrayList<RecommNonGioneeModelBean.RecommNonGioneeModeData> recommNonGioneeModeDatas = recommNonGioneeModelBean.getData();
                for (int i = 0; i < recommNonGioneeModeDatas.size(); i++) {
                    recommNonGioneeModeDatas.get(i).setIsSelected(false);
                }
                recommNonGioneeModelBean.setData(recommNonGioneeModeDatas);
                String brandName = data.getStringExtra("name");
                selBrandId = data.getIntExtra("id", 0);
                selBrandNamePos = data.getIntExtra("brand_name_pos", 0);
                if (brandName != null && !brandName.isEmpty()) {
                    tvBrand.setText(brandName);
                    tvBrand.setTextColor(getResources().getColor(R.color.colorPrimary));
                    rlModel.setVisibility(View.VISIBLE);
                } else {
                    tvBrand.setText(getString(R.string.text_select_brand));
                    tvBrand.setTextColor(getResources().getColor(R.color.half_black));
                    rlModel.setVisibility(View.GONE);
                }
                ArrayList<RecommNonGioneeModelBean.Model> modelList = recommNonGioneeModelBean.getData().get(selBrandNamePos).getModel();
                if (modelList != null && modelList.size() > 0) {
                    for (int i = 0; i < modelList.size(); i++) {
                        modelList.get(i).setIsSelected(false);
                    }
                    recommNonGioneeModelBean.getData().get(selBrandNamePos).setModel(modelList);
                }

                tvModel.setText(getString(R.string.text_select_model));
                tvModel.setTextColor(getResources().getColor(R.color.half_black));
                selModelList.clear();
            } else if (requestCode == 102) {
                ArrayList<RecommNonGioneeModelBean.Model> modelList = recommNonGioneeModelBean.getData().get(selBrandNamePos).getModel();
                if (modelList != null && modelList.size() > 0) {
                    for (int i = 0; i < modelList.size(); i++) {
                        modelList.get(i).setIsSelected(false);
                    }
                    recommNonGioneeModelBean.getData().get(selBrandNamePos).setModel(modelList);
                }
                selModelList = (Map<Integer, RecommNonGioneeModelBean.Model>) data.getSerializableExtra("model_hash_map");
                if (selModelList.size() > 0) {
                    List<RecommNonGioneeModelBean.Model> modelArrayList = new ArrayList<>();
                    for (Map.Entry<Integer, RecommNonGioneeModelBean.Model> map : selModelList.entrySet()) {
                        RecommNonGioneeModelBean.Model model = map.getValue();
                        modelArrayList.add(model);
                    }
                    setSelectedModelNames(modelArrayList);
                } else {
                    tvModel.setText(getString(R.string.text_select_model));
                    tvModel.setTextColor(getResources().getColor(R.color.half_black));
                }
            } else if (requestCode == 103) {
                RecommAttribBean.RecommAttribData recommAttribData = (RecommAttribBean.RecommAttribData) data.getSerializableExtra("attrib_list");
                for (int i = 0; i < llFilterCateg.getChildCount(); i++) {
                    LinearLayout llFilterCategChild = (LinearLayout) llFilterCateg.getChildAt(i);
                    RelativeLayout relativeLayout = (RelativeLayout) llFilterCategChild.getChildAt(0);
                    RelativeLayout relativeLayout1 = (RelativeLayout) relativeLayout.getChildAt(0);
                    TextView tvCatgTitle = (TextView) relativeLayout1.getChildAt(0);
                    TextView tvCatg = (TextView) relativeLayout1.getChildAt(1);
                    if (recommAttribData.getName().equalsIgnoreCase(tvCatgTitle.getText().toString())) {
                        recommAttribBean.getData().get(i).setSelSearchAttrib(recommAttribData.getSelSearchAttrib());
                        List<String> selSearchAttribList = recommAttribData.getSelSearchAttrib();
                        if (selSearchAttribList != null && selSearchAttribList.size() > 0) {
                            setSelectedAttributes(selSearchAttribList, tvCatg);
                        } else {
                            List<String> searchAttribList = recommAttribData.getSearch_attribute();
                            setSearchAttributes(searchAttribList, tvCatg);
                        }
                        break;
                    }
                }
            }
        }
    }

    private void setAttributes() {
        Cursor cursor = DataBaseHandler.getInstance(getActivity()).getAllRecommAttribData();
        if (cursor.moveToFirst()) {
            String attribResponse = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            recommAttribBean = new Gson().fromJson(attribResponse, RecommAttribBean.class);
            if (recommAttribBean.getStatus().equalsIgnoreCase("success")) {
                List<RecommAttribBean.RecommAttribData> recommAttribDataList = recommAttribBean.getData();
                if (recommAttribDataList.size() > 0) {
                    llFilterCateg.removeAllViews();
                    LayoutInflater inflater = (LayoutInflater) getActivity().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                    for (int i = 0; i < recommAttribDataList.size(); i++) {
                        View myView = (View) inflater.inflate(R.layout.row_filter_category, null);
                        TextView tvCatgTitle = (TextView) myView.findViewById(R.id.tv_categ_title);
                        TextView tvCatg = (TextView) myView.findViewById(R.id.tv_categ);
                        final RecommAttribBean.RecommAttribData recommAttribData = recommAttribDataList.get(i);
                        if (!recommAttribData.getName().equalsIgnoreCase("price")) {
                            tvCatgTitle.setText(recommAttribData.getName());
                            List<String> filterListAttrib = recommAttribData.getSearch_attribute();
                            List<String> selFilterListAttrib = recommAttribData.getSelSearchAttrib();
                            if (selFilterListAttrib != null && selFilterListAttrib.size() > 0) {
                                setSelectedAttributes(selFilterListAttrib, tvCatg);
                            } else {
                                if (filterListAttrib.size() > 0) {
                                    setSearchAttributes(filterListAttrib, tvCatg);
                                } else {
                                    tvCatg.setText("");
                                }
                            }
                            myView.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View v) {
                                    Intent intent = new Intent(getActivity(), RecommFilterListActivity.class);
                                    intent.putExtra(UIUtils.RECOMM_KEY_TYPE, UIUtils.RECOMM_VALUE_ATTRIB);
                                    intent.putExtra(UIUtils.RECOMM_KEY_SEL_ATTRIB, recommAttribData);
                                    startActivityForResult(intent, 103);
                                }
                            });
                            llFilterCateg.addView(myView);
                        } else {
                            if (recommAttribData.getSearch_attribute().size() > 0) {
                                String minMaxPrice = recommAttribData.getSearch_attribute().get(0);
                                String[] mMPrice = minMaxPrice.split("-");
                                tvMinPrice.setText(mMPrice[0].trim());
                                tvMaxPrice.setText(mMPrice[1].trim());
                                crystalRangeSeekbar.setRangeValues(Integer.parseInt(mMPrice[0].trim()), Integer.parseInt(mMPrice[1].trim()));
                                crystalRangeSeekbar.setSelectedMinValue(Integer.parseInt(mMPrice[0].trim()));
                                crystalRangeSeekbar.setSelectedMaxValue(Integer.parseInt(mMPrice[1].trim()));
//                                    crystalRangeSeekbar.setMinValue(Float.parseFloat(mMPrice[0].trim()));
//                                    crystalRangeSeekbar.setMaxValue(Float.parseFloat(mMPrice[1].trim()));
                            } else {
                                tvMinPrice.setText("0");
                                tvMaxPrice.setText("50000");
                                crystalRangeSeekbar.setRangeValues(0, 50000);
                                crystalRangeSeekbar.setSelectedMinValue(0);
                                crystalRangeSeekbar.setSelectedMaxValue(50000);
                            }
                            if (recommAttribData.getSelSearchAttrib() != null && recommAttribData.getSelSearchAttrib().size() > 0) {
                                String minMaxPrice = recommAttribData.getSelSearchAttrib().get(0);
                                String[] mMPrice = minMaxPrice.split("-");
                                tvMinPrice.setText(mMPrice[0].trim());
                                tvMaxPrice.setText(mMPrice[1].trim());
                                crystalRangeSeekbar.setSelectedMinValue(Integer.parseInt(mMPrice[0].trim()));
                                crystalRangeSeekbar.setSelectedMaxValue(Integer.parseInt(mMPrice[1].trim()));
//                                crystalRangeSeekbar.setMinValue(Float.parseFloat(mMPrice[0].trim()));
//                                crystalRangeSeekbar.setMaxValue(Float.parseFloat(mMPrice[1].trim()));
                            }
                            setSeekBarListener();
                        }
                    }
                }
            }
        }
    }

    private void setSelectedAttributes(List<String> selSearchAttribList, TextView tvCatg) {
        StringBuilder stringBuilder = new StringBuilder();
        for (int j = 0; j < selSearchAttribList.size(); j++) {
            if (j > 1) {
                stringBuilder.append(" & " + (selSearchAttribList.size() - 2) + " more");
                break;
            } else {
                if (j == 1) {
                    stringBuilder.append(selSearchAttribList.get(j));
                } else {
                    if (j != (selSearchAttribList.size() - 1)) {
                        stringBuilder.append(selSearchAttribList.get(j) + ", ");
                    } else {
                        stringBuilder.append(selSearchAttribList.get(j));
                    }
                }
            }
        }
        tvCatg.setText(stringBuilder.toString());
        tvCatg.setTextColor(getResources().getColor(R.color.colorPrimary));
    }


    private void setSearchAttributes(List<String> searchAttribList, TextView tvCatg) {
        StringBuilder stringBuilder = new StringBuilder();
        for (int j = 0; j < searchAttribList.size(); j++) {
            if (j > 1) {
                stringBuilder.append(" & more");
                break;
            } else {
                if (j == 1) {
                    stringBuilder.append(searchAttribList.get(j));
                } else {
                    if (j != (searchAttribList.size() - 1)) {
                        stringBuilder.append(searchAttribList.get(j) + ", ");
                    } else {
                        stringBuilder.append(searchAttribList.get(j));
                    }
                }
            }
        }
        tvCatg.setText(stringBuilder.toString());
        tvCatg.setTextColor(getResources().getColor(R.color.half_black));
    }
}
