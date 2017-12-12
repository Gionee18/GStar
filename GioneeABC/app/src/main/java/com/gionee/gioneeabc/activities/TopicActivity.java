package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.KeyEvent;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.UpdateCategoryAdaptor;
import com.gionee.gioneeabc.bean.TopicBeanResponse;
import com.gionee.gioneeabc.bean.UpdateResponseBean;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;
import com.gionee.gioneeabc.interfaces.OnLoadMoreListener;
import com.google.gson.Gson;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class TopicActivity extends BaseActivity implements NetworkTask.Result, View.OnClickListener {

    private static final int GET_TOPIC = 101;
    private EditText etSearch;

    private RecyclerView recyclerView;
    UpdateResponseBean.Subcategory subcategory;
    private UpdateCategoryAdaptor adaptor;
    TextView no_result;
    private boolean isChange = false;
    int catID;
    int subCatID;
    private Toolbar toolbar;
    private int page = 1;
    TextView tvClear;
    //    ArrayList<UpdateResponseBean.Topic> searchTopics = new ArrayList<>();
    ArrayList<UpdateResponseBean.Topic> tempTopics = new ArrayList<>();
    private boolean isSearch = false;
    private String searchString = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_topic);
        subcategory = (UpdateResponseBean.Subcategory) getIntent().getSerializableExtra("data");
        toolbar = (Toolbar) findViewById(R.id.tool_bar);

        toolbar.setTitleTextColor(0xFFFFFFFF);
        toolbar.setTitle(subcategory.getSubcategory_name());
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        final Drawable upArrow = getResources().getDrawable(R.drawable.abc_ic_ab_back_material);
        upArrow.setColorFilter(getResources().getColor(R.color.white), PorterDuff.Mode.SRC_ATOP);
        getSupportActionBar().setHomeAsUpIndicator(upArrow);

        init();
    }

    private void init() {

        if (subcategory.getTopic() != null && subcategory.getTopic().size() > 0) {
            catID = subcategory.getTopic().get(0).getCategoryId();
            subCatID = subcategory.getId();
            page = (subcategory.getTopic().size() / NetworkConstants.visibleThreshold) + 1;
        }

        no_result = (TextView) findViewById(R.id.no_result);

        tvClear = (TextView) findViewById(R.id.clear);
        tvClear.setOnClickListener(this);

        etSearch = (EditText) findViewById(R.id.et_search);
        etSearch.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                if (actionId == EditorInfo.IME_ACTION_SEARCH) {
                    searchString = etSearch.getText().toString().trim();
                    if (searchString.length() > 0)
                        search(searchString);
                    else {
                        searchString = "";
                    }
                    return true;
                }
                return false;
            }
        });

        recyclerView = (RecyclerView) findViewById(R.id.recyclerView);

        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        tempTopics.addAll(subcategory.getTopic());
        adaptor = new UpdateCategoryAdaptor(tempTopics, this, recyclerView);

        adaptor.setOnLoadMoreListener(new OnLoadMoreListener() {
            @Override
            public void onLoadMore() {
                Log.e("Load", "Load");
                getLoadMoreTopicFromServer(page, searchString);

            }
        });

        recyclerView.setAdapter(adaptor);
        if (tempTopics.size() == 0)
            no_result.setVisibility(View.VISIBLE);
        else
            no_result.setVisibility(View.GONE);

        Intent intent = getIntent();
        if (intent.hasExtra("type") && intent.getStringExtra("type").toString().equalsIgnoreCase("push")) {
            String topic = intent.getStringExtra("topic");
            for (int k = 0; k < tempTopics.size(); k++) {
                if (tempTopics.get(k).getTopicName().equalsIgnoreCase(topic)) {

                    Intent pushIntent = new Intent(this, UpdateDetailActivity.class);
                    pushIntent.putExtra("data", tempTopics.get(k));
                    startActivityForResult(pushIntent, 1);
                    break;

                }
            }
        }

        if (tempTopics.size() >= 20)
            adaptor.setLoaded();

    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (isChange) {
            Intent intent = new Intent();
            intent.putExtra("Subcategory", subcategory);
            setResult(2, intent);
        }
        Util.hideKeyBoard(this);
        finish();
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        if (isChange) {
            Intent intent = new Intent();
            intent.putExtra("Subcategory", subcategory);
            setResult(2, intent);
        }
        finish();
    }


    private void search(String searchString) {
        Util.hideKeyBoard(this);
        isSearch = true;
        tempTopics.clear();
        adaptor.notifyDataSetChanged();
        if (tempTopics.size() == 0)
            no_result.setVisibility(View.VISIBLE);
        else
            no_result.setVisibility(View.GONE);
        page = 1;
        if (Util.isNetworkAvailable(TopicActivity.this))
            getLoadMoreTopicFromServer(page, searchString);
        else
            loadFromLocal(searchString);
    }

    private void loadFromLocal(String search) {
        for (int i = 0; i < subcategory.getTopic().size(); i++) {
            if (search(subcategory.getTopic().get(i).getTopicName(), search)) {
                tempTopics.add(subcategory.getTopic().get(i));
            }
        }
        adaptor.notifyDataSetChanged();
        if (tempTopics.size() == 0)
            no_result.setVisibility(View.VISIBLE);
        else
            no_result.setVisibility(View.GONE);
    }

    private boolean search(String topic, String searchString) {
        String[] s = topic.split(" ");
        if (s != null && s.length > 0) {
            for (int i = 0; i < s.length; i++) {
                if (s[i].startsWith(searchString))
                    return true;

            }
        }
        return false;
    }


    private void getLoadMoreTopicFromServer(int page, String search) {
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        params.add(new BasicNameValuePair("category_id", catID + ""));
        params.add(new BasicNameValuePair("subcategory_id", subCatID + ""));
        params.add(new BasicNameValuePair("page_no", page + ""));
        params.add(new BasicNameValuePair("search_keyword", search));
        NetworkTask networkTask = new NetworkTask(TopicActivity.this, GET_TOPIC, params, null);
        networkTask.exposePostExecute(TopicActivity.this);
        networkTask.execute(NetworkConstants.GET_MORE_TOPIC);
    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            switch (id) {
                case GET_TOPIC:
                    try {
                        JSONObject jsonObject = new JSONObject(object);
                        if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {

                            TopicBeanResponse topicBeanResponse = new Gson().fromJson(object, TopicBeanResponse.class);
                            if (topicBeanResponse.getData() != null) {
                                if (topicBeanResponse.getData().getTopics() != null && topicBeanResponse.getData().getTopics().size() > 0) {
                                    isChange = true;
                                    if (isSearch == false)
                                        subcategory.getTopic().clear();
                                    tempTopics.clear();
                                    for (int i = 0; i < topicBeanResponse.getData().getTopics().size(); i++) {
                                        tempTopics.add(topicBeanResponse.getData().getTopics().get(i));
                                        if (isSearch == false)
                                            subcategory.getTopic().add(topicBeanResponse.getData().getTopics().get(i));
                                    }
                                    adaptor.notifyDataSetChanged();
                                    if (tempTopics.size() == 0)
                                        no_result.setVisibility(View.VISIBLE);
                                    else
                                        no_result.setVisibility(View.GONE);
                                    if (tempTopics.size() >= page * NetworkConstants.visibleThreshold)
                                        adaptor.setLoaded();
                                    page++;
                                }
                            }
                        }
                    } catch (Exception ex) {

                    }
                    break;
            }
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == 2) {
            isChange = true;
            int Id = data.getIntExtra("Id", 0);
            if (subcategory != null && subcategory.getTopic().size() > 0) {
                for (int i = 0; i < subcategory.getTopic().size(); i++) {
                    if (subcategory.getTopic().get(i).getId() == Id) {
                        subcategory.getTopic().get(i).setIsRead(1);
                        subcategory.setUnreadCount(subcategory.getUnreadCount() - 1);
                        adaptor.notifyDataSetChanged();
                        if (tempTopics.size() == 0)
                            no_result.setVisibility(View.VISIBLE);
                        else
                            no_result.setVisibility(View.GONE);
                        break;
                    }
                }
            }
        }
    }

    @Override
    public void onClick(View view) {
        searchString = "";
        isSearch = false;
        etSearch.setText("");
        tempTopics.clear();
        tempTopics.addAll(subcategory.getTopic());
        page = (subcategory.getTopic().size() / NetworkConstants.visibleThreshold) + 1;
        adaptor.notifyDataSetChanged();
        if (tempTopics.size() == 0)
            no_result.setVisibility(View.VISIBLE);
        else
            no_result.setVisibility(View.GONE);

    }
}
