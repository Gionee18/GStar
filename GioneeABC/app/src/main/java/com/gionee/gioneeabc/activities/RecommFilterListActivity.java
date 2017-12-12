package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.RecommFilterListAdapter;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.google.gson.Gson;

import java.io.Serializable;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class RecommFilterListActivity extends BaseActivity implements View.OnClickListener {

    private Toolbar toolbar;
    private TextView tvApply, tvClear;
    private RecyclerView recyclerView;
    private RecommFilterListAdapter adapter;
    private Window window;
    private List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList;
    private LinearLayout llApply;

    private String type;
    private int brandNamePos;
    private Map<Integer, RecommNonGioneeModelBean.Model> selModelMap = new HashMap<>();
    private RecommAttribBean.RecommAttribData recommAttribData;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recomm_filter_list);
        Bundle bundle = getIntent().getExtras();
        if (bundle != null) {
            if (bundle.getString(UIUtils.RECOMM_KEY_TYPE) != null) {
                type = bundle.getString(UIUtils.RECOMM_KEY_TYPE);
                brandNamePos = bundle.getInt(UIUtils.RECOMM_KEY_BRAND_NAME_POS);
                selModelMap = (Map<Integer, RecommNonGioneeModelBean.Model>) bundle.getSerializable(UIUtils.RECOMM_KEY_SEL_MODEL);
                recommAttribData = (RecommAttribBean.RecommAttribData) bundle.getSerializable(UIUtils.RECOMM_KEY_SEL_ATTRIB);
            }
        }
        initUI();
        setToolbar();
        initListener();
        GStarApplication.getInstance().trackScreenView(getString(R.string.title_recommender));
        window = this.getWindow();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        setFilterAdapter();
    }

    private void initUI() {
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        tvApply = (TextView) findViewById(R.id.tv_apply);
        tvClear = (TextView) findViewById(R.id.tv_clear);
        llApply = (LinearLayout) findViewById(R.id.ll_apply);
        recyclerView = (RecyclerView) findViewById(R.id.recyclerView);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));

        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND)) {
            llApply.setVisibility(View.GONE);
        } else {
            llApply.setVisibility(View.VISIBLE);
        }
    }

    private void setToolbar() {
        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND))
            toolbar.setTitle(getString(R.string.text_brand));
        else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL))
            toolbar.setTitle(getString(R.string.text_model));
        else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB))
            toolbar.setTitle(recommAttribData.getName());
        else
            toolbar.setTitle(getString(R.string.title_recommender));
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }

    private void initListener() {
        tvApply.setOnClickListener(this);
        tvClear.setOnClickListener(this);
    }

    private void setFilterAdapter() {
        if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_BRAND) || type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL)) {
            Cursor cursor = DataBaseHandler.getInstance(this).getAllRecommModelData();
            if (cursor.moveToFirst()) {
                String modelResponse = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
                RecommNonGioneeModelBean recommNonGioneeModelBean = new Gson().fromJson(modelResponse, RecommNonGioneeModelBean.class);
                if (recommNonGioneeModelBean.getStatus().equalsIgnoreCase("success")) {
                    brandNameList = recommNonGioneeModelBean.getData();
                    adapter = new RecommFilterListAdapter(this, brandNameList, type, brandNamePos, recommAttribData);
                    adapter.setSelModelList(selModelMap);
                    recyclerView.setAdapter(adapter);
                }
            }
        } else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)) {
            adapter = new RecommFilterListAdapter(this, brandNameList, type, brandNamePos, recommAttribData);
            recyclerView.setAdapter(adapter);
        }
    }

    @Override
    public void onClick(View v) {
        if (v == tvApply) {
            if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL)) {
                if (adapter != null) {
                    Intent intent = new Intent();
                    intent.putExtra("model_hash_map", (Serializable) adapter.getSelModelList());
                    setResult(102, intent);
                    finish();
                }
            } else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)) {
                if (adapter != null) {
                    Intent intent = new Intent();
                    intent.putExtra("attrib_list", adapter.getRecommAttribData());
                    setResult(103, intent);
                    finish();
                }
            }
        } else if (v == tvClear) {
            if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_MODEL)) {
                if (adapter != null) {
                    Map<Integer, RecommNonGioneeModelBean.Model> modelMap = adapter.getSelModelList();
                    modelMap.clear();
                    adapter.setSelModelList(modelMap);
                    adapter.notifyDataSetChanged();
                }
            } else if (type.equalsIgnoreCase(UIUtils.RECOMM_VALUE_ATTRIB)) {
                if (adapter != null) {
                    List<String> attribDataList = recommAttribData.getSelSearchAttrib();
                    if (attribDataList != null)
                        attribDataList.clear();//amit
                    recommAttribData.setSelSearchAttrib(attribDataList);
                    adapter.setRecommAttribData(recommAttribData);
                    adapter.notifyDataSetChanged();
                }
            }
        }
    }
}
