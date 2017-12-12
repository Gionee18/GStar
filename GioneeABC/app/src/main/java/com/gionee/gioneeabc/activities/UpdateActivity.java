package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.os.Handler;
import android.support.design.widget.AppBarLayout;
import android.support.design.widget.CollapsingToolbarLayout;
import android.support.design.widget.TabLayout;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.ImagesAdapter;
import com.gionee.gioneeabc.adapters.ProductsAdapter;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.UpdateResponseBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.UpdateSubCatFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.google.gson.Gson;

import org.json.JSONObject;

import java.util.List;

import me.relex.circleindicator.CircleIndicator;

/**
 * Created by root on 7/10/16.
 */
public class UpdateActivity extends BaseActivity implements NetworkTask.Result {
    Toolbar toolbar;
    ViewPager viewPager;
    TabLayout tabLayout;
    Window window;
    CollapsingToolbarLayout collapsingToolbarLayout;
    AppBarLayout appBarLayout;
    public ProductBean product;
    ProductsAdapter productsAdapter;
    DataBaseHandler dbHandler;
    List<ImageBean> imageList;
    ImagesAdapter adapter = null;
    CircleIndicator ipIndicator;
    public UpdateResponseBean updateResponseBean;
    android.os.Handler handler;
    private static final int SET_READ = 101;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.update_screen);

        UserBean User = DataBaseHandler.getInstance(this).getUser();
        DataBaseHandler.getInstance(this).addModuleAuditTrailData(User.getUserId(), "Updates", System.currentTimeMillis(), DataStore.getLastLogin(this));
        product = (ProductBean) getIntent().getSerializableExtra("Updates");
        dbHandler = DataBaseHandler.getInstance(UpdateActivity.this);
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        toolbar.setTitle("Updates");
        handler = new android.os.Handler();
        GStarApplication.getInstance().trackScreenView("Product Detail Screen");

        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        ipIndicator = (CircleIndicator) findViewById(R.id.indicator);
        window = UpdateActivity.this.getWindow();
        appBarLayout = (AppBarLayout) findViewById(R.id.appBarLayout);
        collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);

        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent();
                intent.putExtra("clean", false);
                setResult(101, intent);
                finish();
            }
        });


        Cursor cursor = DataBaseHandler.getInstance(this).getAllUpdateCategory();
        if (cursor.moveToFirst()) {
            String updateCatRes = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));

            updateResponseBean = new Gson().fromJson(updateCatRes, UpdateResponseBean.class);
        }

        viewPager = (ViewPager) findViewById(R.id.viewPager);
        viewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int position) {
                tabLayout.getTabAt(position).select();
            }

            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });
//

        setupViewPager(viewPager);
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        tabLayout.setupWithViewPager(viewPager);
//        viewPager.addOnPageChangeListener(new TabLayout.TabLayoutOnPageChangeListener(tabLayout));
        setTabLayoutDivider();


        final Intent intent = getIntent();
        if (intent.hasExtra("type") && intent.getStringExtra("type").toString().equalsIgnoreCase("push")) {
            new Handler().postDelayed(new Runnable() {
                @Override
                public void run() {
                    String topic = intent.getStringExtra("topic");
                    String category = intent.getStringExtra("category");
                    String subcategory = intent.getStringExtra("subcategory");
                    boolean isFound = false;
                    for (int i = 0; i < updateResponseBean.getData().size(); i++) {
                        if (updateResponseBean.getData().get(i).getSubcategory() != null && updateResponseBean.getData().get(i).getSubcategory().size() > 0 && updateResponseBean.getData().get(i).getCategoryName().equalsIgnoreCase(category)) {
                            {
                                for (int j = 0; j < updateResponseBean.getData().get(i).getSubcategory().size(); j++) {
                                    if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic() != null && updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size() > 0 && updateResponseBean.getData().get(i).getSubcategory().get(j).getSubcategory_name().equalsIgnoreCase(subcategory)) {
                                        {
                                            for (int k = 0; k < updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size(); k++) {
                                                if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().get(k).getTopicName().equalsIgnoreCase(topic)) {

                                                    Intent pushIntent = new Intent(UpdateActivity.this, TopicActivity.class);
                                                    pushIntent.putExtra("data", updateResponseBean.getData().get(i).getSubcategory().get(j));
                                                    pushIntent.putExtra("type", "push");
                                                    pushIntent.putExtra("topic", topic);
                                                    startActivityForResult(pushIntent, 1);
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
            }, 300);

        }

    }


    private void setupViewPager(ViewPager viewPager) {
        productsAdapter = new ProductsAdapter(getSupportFragmentManager());
        if (updateResponseBean != null) {
            for (int i = 0; i < updateResponseBean.getData().size(); i++) {

                int totalSubCatTopicCount = 0;
                UpdateSubCatFragment updateSubCatFragment = new UpdateSubCatFragment();
                Bundle bundle=new Bundle();
                bundle.putInt("position",i);
                updateSubCatFragment.setArguments(bundle);
                String title = updateResponseBean.getData().get(i).getCategoryName();
                productsAdapter.addFragment(updateSubCatFragment, title);
                if (updateResponseBean.getData().get(i).getSubcategory() != null && updateResponseBean.getData().get(i).getSubcategory().size() > 0) {
                    for (int j = 0; j < updateResponseBean.getData().get(i).getSubcategory().size(); j++) {
                        if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic() != null && updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size() > 0) {
                            int count = 0;
                            for (int k = 0; k < updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().size(); k++) {
                                if (updateResponseBean.getData().get(i).getSubcategory().get(j).getTopic().get(k).getIsRead() == 0) {
                                    count++;
                                    totalSubCatTopicCount++;
                                }
                            }
                            updateResponseBean.getData().get(i).getSubcategory().get(j).setUnreadCount(count);
                        }
                    }
                    updateResponseBean.getData().get(i).setUnreadCount(totalSubCatTopicCount);
                }
            }
        }

        viewPager.setAdapter(productsAdapter);

        Log.e("Mytest", "Mytest");
    }

    void setTabLayoutDivider() {

        viewPager.setAdapter(productsAdapter);
        tabLayout.setupWithViewPager(viewPager);

        for (int i = 0; i < tabLayout.getTabCount(); i++) {
            int count = updateResponseBean.getData().get(i).getUnreadCount();
            TabLayout.Tab tab = tabLayout.getTabAt(i);
            RelativeLayout relativeLayout = (RelativeLayout) LayoutInflater.from(this).inflate(R.layout.tab_layout_category, tabLayout, false);
            TextView tabTextView = (TextView) relativeLayout.findViewById(R.id.tab_title);
            TextView tabTvCount = (TextView) relativeLayout.findViewById(R.id.tv_new);
            tabTextView.setText(tab.getText());
            if (count > 0)
                tabTvCount.setText(count + "");
            else
                tabTvCount.setVisibility(View.GONE);
            if (i == tabLayout.getTabCount() - 1)
                relativeLayout.findViewById(R.id.divider_view).setVisibility(View.GONE);
            tab.setCustomView(relativeLayout);
            tab.select();
        }
        viewPager.setCurrentItem(0);
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent();
        intent.putExtra("clean", false);
        setResult(101, intent);
        finish();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == 2 && data != null) {
            int postion = 0;
            UpdateResponseBean.Subcategory subcategory = (UpdateResponseBean.Subcategory) data.getSerializableExtra("Subcategory");
            if (updateResponseBean.getData() != null && updateResponseBean.getData().size() > 0) {
                for (int i = 0; i < updateResponseBean.getData().size(); i++) {
                    if (updateResponseBean.getData().get(i).getSubcategory() != null && updateResponseBean.getData().get(i).getSubcategory().size() > 0)
                        for (int j = 0; j < updateResponseBean.getData().get(i).getSubcategory().size(); j++) {
                            if (updateResponseBean.getData().get(i).getSubcategory().get(j).getId() == subcategory.getId()) {
                                updateResponseBean.getData().get(i).getSubcategory().set(j, subcategory);
                                postion = i;
                                break;
                            }
                        }
                }
            }

            int totalSubCatTopicCount = 0;
            for (int j = 0; j < updateResponseBean.getData().get(postion).getSubcategory().size(); j++) {
                int count = 0;
                for (int k = 0; k < updateResponseBean.getData().get(postion).getSubcategory().get(j).getTopic().size(); k++) {
                    if (updateResponseBean.getData().get(postion).getSubcategory().get(j).getTopic().get(k).getIsRead() == 0) {
                        count++;
                        totalSubCatTopicCount++;
                    }
                }
                updateResponseBean.getData().get(postion).getSubcategory().get(j).setUnreadCount(count);
            }
            updateResponseBean.getData().get(postion).setUnreadCount(totalSubCatTopicCount);

            if (tabLayout != null)
                for (int i = 0; i < tabLayout.getTabCount(); i++) {
                    int count = updateResponseBean.getData().get(i).getUnreadCount();

                    TabLayout.Tab tab = tabLayout.getTabAt(i);
                    View view = tab.getCustomView();

                    TextView tabTvCount = (TextView) view.findViewById(R.id.tv_new);
                    if (count > 0)
                        tabTvCount.setText(count + "");
                    else
                        tabTvCount.setVisibility(View.GONE);
                }

            UpdateSubCatFragment viewPagerFragment = (UpdateSubCatFragment) productsAdapter.getItem(postion);
            viewPagerFragment.refresh();

            Gson gson = new Gson();
            String str = gson.toJson(updateResponseBean);
            DataBaseHandler.getInstance(this).deleteAllUpdateCategory();
            DataBaseHandler.getInstance(this).addGetData(str, DataBaseHandler.TYPE_UPDATE_CATEGORY);

        }
    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
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
