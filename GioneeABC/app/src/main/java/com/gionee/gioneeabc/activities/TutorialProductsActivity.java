package com.gionee.gioneeabc.activities;


import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.os.Handler;
import android.support.design.widget.TabLayout;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TabHost;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.ProductsAdapter;
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.TutorialProductListFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.google.gson.Gson;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;


/**
 * Created by root on 4/10/16.
 */
public class TutorialProductsActivity extends BaseActivity implements TabHost.OnTabChangeListener {

    private Toolbar toolbar;
    private Window window;
    public ViewPager viewPager;
    private TabLayout tabLayout;
//    private CollapsingToolbarLayout collapsingToolbarLayout;
    public List<TutorialResponseBean.TutorialDataCatogaryBean> categoryList;
    public static List<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> productsList;
    public List<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> newProductsList;
    private ProductsAdapter adapter;
    private DownloadManager downloadmanager;
    private BroadcastReceiver catImagereceiver;
//    private long enqueue;
    private ImageView ivHeader;
    public static int selectedPage = 0;
    private HashMap<Long, Integer> hm;
    public boolean cleanProductList = true;
    private List<TutorialProductListFragment> fragList;
    private Handler handler;
    private TutorialProductListFragment frag;
    public static TutorialResponseBean tutorialDataCatogaryBean;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.product_screen);
        UserBean User = DataBaseHandler.getInstance(this).getUser();
        DataBaseHandler.getInstance(this).addModuleAuditTrailData(User.getUserId(), "Tutorials", System.currentTimeMillis(), DataStore.getLastLogin(this));
        initId();
        toolbar.setTitle("Tutorials");
        setSupportActionBar(toolbar);
        toolbar.setOverflowIcon(getResources().getDrawable(R.drawable.dots));

        ((ImageView)findViewById(R.id.back)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });

        handler = new Handler();
        GStarApplication.getInstance().trackScreenView("Tutorials List Screen");

        hm = new HashMap<Long, Integer>();
        window = TutorialProductsActivity.this.getWindow();

        fragList = new ArrayList<TutorialProductListFragment>();
        categoryList = new ArrayList<TutorialResponseBean.TutorialDataCatogaryBean>();
        productsList = new ArrayList<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean>();
        newProductsList = new ArrayList<>();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

        tutorialDataCatogaryBean = new TutorialResponseBean();
        Cursor cursor = DataBaseHandler.getInstance(this).getAllTutorialCategory();
        if (cursor.moveToFirst()) {
            String tutorialCatResponse = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));

            tutorialDataCatogaryBean = new Gson().fromJson(tutorialCatResponse, TutorialResponseBean.class);
            categoryList = tutorialDataCatogaryBean.getData();
        }

        viewPager.setOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int position) {
                selectedPage = position;
                frag = fragList.get(position);
                handler.postDelayed(startApp, NetworkConstants.PAGE_REFRESH_TIME);
            }

            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });

        if (categoryList.size() > 0)
            setupViewPager();

    }

    private void initId() {
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        viewPager = (ViewPager) findViewById(R.id.viewPager);
//        collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);
        ivHeader = (ImageView) findViewById(R.id.ivHeader);
        tabLayout = (TabLayout) findViewById(R.id.tabs);
    }


    Runnable startApp = new Runnable() {
        @Override
        public void run() {
            try {
                if (categoryList.get(selectedPage).getCat_image().size() > 0) {
                    Picasso.with(TutorialProductsActivity.this).load(NetworkConstants.BASE_URL + "/" + categoryList.get(selectedPage).getCat_image().get(0).getPath() + "/" + categoryList.get(selectedPage).getCat_image().get(0).getName()).into(ivHeader);
                }else {
                    ivHeader.setImageResource(R.color.colorPrimary);
                }
            }catch (Exception e){
                ivHeader.setImageResource(R.color.colorPrimary);
            }
            frag.fetchData();

        }
    };


    private void addCategoryInAdapter() {
        //adapter.addFragment(new ProductListFragment(), categoryList.get(categoryList.size()-1).getCategoryName());
        for (int i = 0; i < categoryList.size(); i++) {
            TutorialProductListFragment frag = new TutorialProductListFragment();
            fragList.add(frag);
            adapter.addFragment(frag, categoryList.get(i).getCategory_name());
        }
    }

    private void setupViewPager() {
        adapter = new ProductsAdapter(getSupportFragmentManager());
        addCategoryInAdapter();
        setTabLayoutDivider();
        try {
            if (categoryList.get(0).getCat_image().size() > 0)
                Picasso.with(TutorialProductsActivity.this).load(NetworkConstants.BASE_URL + "/" + categoryList.get(0).getCat_image().get(0).getPath() + "/" + categoryList.get(0).getCategory_name()).into(ivHeader);
            else
                ivHeader.setImageResource(R.color.colorPrimary);
        } catch (Exception e) {
            ivHeader.setImageResource(R.color.colorPrimary);
        }
    }

    void setTabLayoutDivider() {
        viewPager.setAdapter(adapter);
        tabLayout.setupWithViewPager(viewPager);
        //     viewPager.setOffscreenPageLimit(0);

        for (int i = 0; i < tabLayout.getTabCount(); i++) {
            TabLayout.Tab tab = tabLayout.getTabAt(i);
            RelativeLayout relativeLayout = (RelativeLayout)
                    LayoutInflater.from(this).inflate(R.layout.tab_layout, tabLayout, false);
            TextView tabTextView = (TextView) relativeLayout.findViewById(R.id.tab_title);
            tabTextView.setText(tab.getText());
            if (i == tabLayout.getTabCount() - 1)
                relativeLayout.findViewById(R.id.divider_view).setVisibility(View.GONE);
            tab.setCustomView(relativeLayout);
            tab.select();
        }
        viewPager.setCurrentItem(0);
    }

    @Override
    protected void onPause() {
        super.onPause();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 101)
            cleanProductList = data.getBooleanExtra("clean", false);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void onTabChanged(String tabId) {

    }
}


