package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.os.Bundle;
import android.support.design.widget.TabLayout;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.ViewPagerRecommenderAdapter;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;

public class RecomenderActivity extends BaseActivity {

    private Toolbar toolbar;
    private TabLayout tabLayout;
    private ViewPager viewPager;
    private Window window;
    private ViewPagerRecommenderAdapter adapter;
    private boolean isManufacturer = true;
//    private MaterialSearchView searchView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recomender);
        UserBean User = DataBaseHandler.getInstance(this).getUser();
        DataBaseHandler.getInstance(this).addModuleAuditTrailData(User.getUserId(), "Recommender", System.currentTimeMillis(), DataStore.getLastLogin(this));
        Bundle bundle = getIntent().getExtras();
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
        initUI();
        setToolbar();
        GStarApplication.getInstance().trackScreenView(getString(R.string.title_recommender));
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

        tabLayout.addTab(tabLayout.newTab().setText(getString(R.string.text_filter_by_manufacturer)));
        tabLayout.addTab(tabLayout.newTab().setText(getString(R.string.text_filter_by_atributes)));
        tabLayout.addTab(tabLayout.newTab().setText(getString(R.string.title_compare)));
//        tabLayout.setTabMode(TabLayout.MODE_SCROLLABLE);  // scorllable tab
        tabLayout.setTabGravity(TabLayout.GRAVITY_FILL);

        adapter = new ViewPagerRecommenderAdapter(this, getSupportFragmentManager(), getIntent().getExtras().getString("from"),
                isManufacturer, getIntent().getExtras().getString("gionee_id"));
        viewPager.setAdapter(adapter);
        viewPager.setOffscreenPageLimit(2);
        if (getIntent().getExtras().getString("gionee_id") != null)
            viewPager.setCurrentItem(2);
        else {
            if (isManufacturer)
                viewPager.setCurrentItem(0);
            else
                viewPager.setCurrentItem(1);
        }
        viewPager.addOnPageChangeListener(new TabLayout.TabLayoutOnPageChangeListener(tabLayout));

        tabLayout.setOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
            @Override
            public void onTabSelected(TabLayout.Tab tab) {
                viewPager.setCurrentItem(tab.getPosition());
                if (tab.getPosition() == 0)
                    isManufacturer = true;
                else if (tab.getPosition() == 1)
                    isManufacturer = false;
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {

            }

            @Override
            public void onTabReselected(TabLayout.Tab tab) {
                viewPager.setCurrentItem(tab.getPosition());
                if (tab.getPosition() == 0)
                    isManufacturer = true;
                else if (tab.getPosition() == 1)
                    isManufacturer = false;
            }
        });
    }

    private void initUI() {
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        tabLayout = (TabLayout) findViewById(R.id.tabLayout);
        viewPager = (ViewPager) findViewById(R.id.viewPager);
//        searchView = (MaterialSearchView) findViewById(R.id.search_view);
        window = this.getWindow();
    }

    private void setToolbar() {
        toolbar.setTitle(getString(R.string.title_recommender));
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });
    }

    /*public MaterialSearchView getSearchView() {
        return searchView;
    }*/

    @Override
    public void onBackPressed() {
        Util.hideKeyBoard(this);
        if (UIUtils.isFilterFromProduct) {
            UIUtils.isFilterFromProduct = false;
            if (Util.isBrandModelSelected(this)) {
                callRecomenderActivity();
            } else {
                if (isManufacturer) {
                    UIUtils.compareSpecficationBean = null;
                    UIUtils.selectedGioneeModel = -1;
                    UIUtils.selectedNonGioneeModel = -1;
                    super.onBackPressed();
                } else
                    callRecomenderActivity();
            }
        } else {
            UIUtils.compareSpecficationBean = null;
            UIUtils.selectedGioneeModel = -1;
            UIUtils.selectedNonGioneeModel = -1;
            super.onBackPressed();
        }
        /*if (UIUtils.isFilterFromProduct) {
            UIUtils.isFilterFromProduct = false;
        }
        super.onBackPressed();*/
    }

    private void callRecomenderActivity() {
        Intent intent = new Intent(this, RecomenderActivity.class);
        if (isManufacturer)
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
        else
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_ATTRIB);
        intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_FILTER);
        startActivity(intent);
        this.finish();
        overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
    }

    public ViewPagerRecommenderAdapter getAdapter() {
        return adapter;
    }

    /*@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        super.onCreateOptionsMenu(menu);
        new MenuInflater(getApplication()).inflate(R.menu.menu_recommender, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.compare) {
            Intent intent = new Intent(this, CompareSpecifictionActivity.class);
            startActivity(intent);
        }
        return super.onOptionsItemSelected(item);
    }*/
}
