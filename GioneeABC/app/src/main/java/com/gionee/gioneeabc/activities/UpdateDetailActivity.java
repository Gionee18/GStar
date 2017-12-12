package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.database.Cursor;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.view.MenuItem;
import android.webkit.WebSettings;
import android.webkit.WebView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.UpdateResponseBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTaskNew;
import com.gionee.gioneeabc.helpers.Util;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONObject;

public class UpdateDetailActivity extends BaseActivity implements NetworkTaskNew.Result {

    private static final int SET_READ = 101;
    private WebView wvSpecification;

    private Toolbar toolbar;
    private UpdateResponseBean.Topic topic;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_update_detail);

        topic = (UpdateResponseBean.Topic) getIntent().getSerializableExtra("data");
        if (topic != null && topic.getIsRead() == 0) {
            setReadStatusOnLocal();
            setReadStatusOnServer();

        }


        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        toolbar.setTitle(topic.getTopicName());
        toolbar.setTitleTextColor(0xFFFFFFFF);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
       final Drawable upArrow = getResources().getDrawable(R.drawable.abc_ic_ab_back_material);
        upArrow.setColorFilter(getResources().getColor(R.color.white), PorterDuff.Mode.SRC_ATOP);
        getSupportActionBar().setHomeAsUpIndicator(upArrow);


        init();
    }

    @Override
    public void onBackPressed() {
        if (topic.getIsRead() == 0) {
            Intent intent = new Intent();
            intent.putExtra("CategoryId", topic.getCategoryId());
            intent.putExtra("SubcategoryId", topic.getSubcategoryId());
            intent.putExtra("Id", topic.getId());
            setResult(2, intent);
        }
        finish();
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (topic.getIsRead() == 0) {
            Intent intent = new Intent();
            intent.putExtra("CategoryId", topic.getCategoryId());
            intent.putExtra("SubcategoryId", topic.getSubcategoryId());
            intent.putExtra("Id", topic.getId());
            setResult(2, intent);
        }
        finish();
        return super.onOptionsItemSelected(item);

    }

    private void init() {
        wvSpecification = (WebView) findViewById(R.id.wvSpecification);
        wvSpecification.setVerticalScrollBarEnabled(true);
        WebSettings settings = wvSpecification.getSettings();
        settings.setDefaultTextEncodingName("utf-8");
        wvSpecification.loadDataWithBaseURL(null, topic.getTopicDesc(), "text/html", "UTF-8", null);
    }

    private void setReadStatusOnLocal() {
        UpdateResponseBean updateResponseBean = null;
        Cursor cursor = DataBaseHandler.getInstance(this).getAllUpdateCategory();
        if (cursor.moveToFirst()) {
            String updateCatRes = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));

            updateResponseBean = new Gson().fromJson(updateCatRes, UpdateResponseBean.class);
        }

        boolean isFound = false;
        for (int i = 0; i < updateResponseBean.getData().size(); i++) {
            if (updateResponseBean.getData().get(i).getSubcategory() != null && updateResponseBean.getData().get(i).getSubcategory().size() > 0 && updateResponseBean.getData().get(i).getId() == topic.getCategoryId()) {
                {
                    for (int j = 0; j < updateResponseBean.getData().get(i).getSubcategory().size(); j++) {
                        if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic() != null && updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size() > 0 && updateResponseBean.getData().get(i).getSubcategory().get(j).getId() == topic.getSubcategoryId()) {
                            {
                                for (int k = 0; k < updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size(); k++) {
                                    if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().get(k).getId() == topic.getId()) {
                                        updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().get(k).setIsRead(1);
                                        Gson gson = new Gson();
                                        String str = gson.toJson(updateResponseBean);
                                        DataBaseHandler.getInstance(this).deleteAllUpdateCategory();
                                        DataBaseHandler.getInstance(this).addGetData(str, DataBaseHandler.TYPE_UPDATE_CATEGORY);
                                        isFound = true;
                                        break;

                                    }
                                }
                            }
                        }
                        if (isFound)
                            break;
                    }
                }
            }
            if (isFound)
                break;
        }


    }


    private void setReadStatusOnServer() {
        UserBean User = DataBaseHandler.getInstance(this).getUser();
        if (Util.isNetworkAvailable(this)) {
            JSONObject Obj = new JSONObject();
            JSONArray jsonArray = new JSONArray();
            JSONObject jsonObject;
            jsonObject = new JSONObject();
            try {
                jsonObject.put("category_id", topic.getCategoryId());
                jsonObject.put("subcategory_id", topic.getSubcategoryId());
                jsonObject.put("topic_id", topic.getId());
                jsonArray.put(jsonObject);
                Obj.put("data", jsonArray);
                Obj.put("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN));
                Obj.put("user_id", User.getUserId());
                NetworkTaskNew networkTask = new NetworkTaskNew(this, SET_READ);
                networkTask.exposePostExecute(this);
                networkTask.execute(NetworkConstants.SET_CATEGORY_READ_URL, Obj.toString());
            } catch (Exception ex) {

            }
        } else {
            DataBaseHandler.getInstance(this).addReadStatusData(User.getUserId(), topic.getCategoryId(), topic.getSubcategoryId(), topic.getId());
        }

    }


    @Override
    public void resultFromNetwork(String object, int id, int arg1, String arg2) {
        if (object != null && !object.equals("")) {
            try {
                JSONObject jsonObject = new JSONObject(object);

                if (jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success")) {

                }

            } catch (Exception ex) {

            }
        }
    }
}
