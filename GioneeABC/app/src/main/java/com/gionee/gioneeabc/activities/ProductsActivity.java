package com.gionee.gioneeabc.activities;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.support.design.widget.CollapsingToolbarLayout;
import android.support.design.widget.TabLayout;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TabHost;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.ProductsAdapter;
import com.gionee.gioneeabc.bean.CategoryBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.ProductListFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.Util;
import com.squareup.picasso.Picasso;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class ProductsActivity extends BaseActivity implements TabHost.OnTabChangeListener {

    Toolbar toolbar;
    Window window;
    public ViewPager viewPager;
    TabLayout tabLayout;
    CollapsingToolbarLayout collapsingToolbarLayout;
    DataBaseHandler dbHandler;

    private final int GET_CATEGORY = 101;
    private final int NEW_CATEGORY = 102;
    public List<CategoryBean> categoryList;
    public List<ProductBean> productsList;
    public List<ProductBean> newProductsList;
    ProductsAdapter adapter;
    DownloadManager downloadmanager;
    BroadcastReceiver catImagereceiver;
    long enqueue;
    String url = null;
    CategoryBean selectedCategory;
    ImageView ivHeader;
    public int selectedPage = 0;
    int position;
    HashMap<Long, Integer> hm;
    Animation animZoomIn;
    String imagePath = "";
    public boolean cleanProductList = true;
    TextView tvHeaderText;
    private List<ProductListFragment> fragList;
    Handler handler;
    ProductListFragment frag;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.product_screen);
        UserBean User = DataBaseHandler.getInstance(this).getUser();
        DataBaseHandler.getInstance(this).addModuleAuditTrailData(User.getUserId(), "Products", System.currentTimeMillis(),DataStore.getLastLogin(this));
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        toolbar.setTitle("Products");
        setSupportActionBar(toolbar);toolbar.setOverflowIcon(getResources().getDrawable(R.drawable.dots));

        ((ImageView)findViewById(R.id.back)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        handler = new Handler();

        GStarApplication.getInstance().trackScreenView("Product List Screen");


        hm = new HashMap<Long, Integer>();
        window = ProductsActivity.this.getWindow();

        fragList = new ArrayList<ProductListFragment>();

        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);


        collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);

        ivHeader = (ImageView) findViewById(R.id.ivHeader);


        viewPager = (ViewPager) findViewById(R.id.viewPager);
        viewPager.setOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int position) {
                selectedPage = position;
                frag = fragList.get(position);
                handler.postDelayed(startApp, NetworkConstants.PAGE_REFRESH_TIME);

                if (categoryList.get(position).getImageLocalPath() != null && !categoryList.get(position).getImageLocalPath().equals("")) {


                    imagePath = categoryList.get(position).getImageLocalPath();

                    Picasso.with(ProductsActivity.this).load(new File(categoryList.get(position).getImageLocalPath())).into(ivHeader);


                } else {

                    Picasso.with(ProductsActivity.this).load(NetworkConstants.BASE_URL + "/" + categoryList.get(position).getImageServerPath() + "/" + categoryList.get(position).getCategoryImage()).into(ivHeader);


                    collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);

                }
            }

            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });


        tabLayout = (TabLayout) findViewById(R.id.tabs);
        dbHandler = DataBaseHandler.getInstance(ProductsActivity.this);
        // getNewProductFromServer();
        categoryList = new ArrayList<CategoryBean>();
        productsList = new ArrayList<ProductBean>();
        newProductsList = new ArrayList<>();
        new FetchingDataFromDBAsyncTask().execute();
//        new FetchindDataFromDB().run();


        /*if (Util.isNetworkAvailable(ProductsActivity.this))
            getNewProductFromServer();
        else
            getCategoriesFromLocal();
        *///}

/*
        setupViewPager(viewPager);
        tabLayout.setupWithViewPager(viewPager);
*/
        downloadmanager = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
        dbHandler = DataBaseHandler.getInstance(ProductsActivity.this);
        catImagereceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
                    long downloadId = intent.getLongExtra(
                            DownloadManager.EXTRA_DOWNLOAD_ID, 0);
                    DownloadManager.Query query = new DownloadManager.Query();
                    query.setFilterById(enqueue);
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c.getInt(columnIndex)) {
                            //  Util.createToast(context, "Download completed... Please check your data in Requester Document folder");
                            // selectedCategory.setImageLocalPath(Environment.getExternalStorageDirectory() + "/GioneeStar/" + selectedCategory.getCategoryImage());
                            if (hm != null && hm.size() > 0) {
                                if (hm.containsKey(downloadId)) {
                                    int pos = hm.get(downloadId);
                                    dbHandler.updateCategoryLocalUrl(categoryList.get(pos).getCategoryId(), Environment.getExternalStorageDirectory() + "/GioneeStar/" + categoryList.get(pos).getCategoryImage());
                                }
                            }
                        }
                        //else
                        // Util.createToast(context, "Download fail, please try again");
                    }
                }
            }
        };
        try {
            registerReceiver(catImagereceiver, new IntentFilter(
                    DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        } catch (Exception e) {
            e.printStackTrace();
        }

    }


    Runnable startApp = new Runnable() {
        @Override
        public void run() {
            frag.fetchData();

        }
    };





    private void addCategoryInAdapter() {
        //adapter.addFragment(new ProductListFragment(), categoryList.get(categoryList.size()-1).getCategoryName());
        for (int i = 0; i < categoryList.size(); i++) {

            ProductListFragment frag = new ProductListFragment();
            fragList.add(frag);
            adapter.addFragment(frag, categoryList.get(i).getCategoryName());
        }
    }

    private void setupViewPager(ViewPager viewPager) {
        adapter = new ProductsAdapter(getSupportFragmentManager());
        if (productsList.size() > 0) {
            addProductsIntoDataBase();
        }
        if (categoryList.size() > 0) {
            addCategoriesIntoDataBase();
        }
        categoryList = dbHandler.getAllCategories();
        addCategoryInAdapter();
        setTabLayoutDivider();
        Picasso.with(ProductsActivity.this).load(NetworkConstants.BASE_URL + "/" + categoryList.get(0).getImageServerPath() + "/" + categoryList.get(0).getCategoryImage()).into(ivHeader);

    }

    void setTabLayoutDivider() {
        viewPager.setAdapter(adapter);
        tabLayout.setupWithViewPager(viewPager);
        //     viewPager.setOffscreenPageLimit(0);

        for (int i = 0; i < tabLayout.getTabCount(); i++) {
            TabLayout.Tab tab = tabLayout.getTabAt(i);
            RelativeLayout relativeLayout = (RelativeLayout) LayoutInflater.from(this).inflate(R.layout.tab_layout, tabLayout, false);
            TextView tabTextView = (TextView) relativeLayout.findViewById(R.id.tab_title);
            tabTextView.setText(tab.getText());
//            if (i == tabLayout.getTabCount() - 1)
//                relativeLayout.findViewById(R.id.divider_view).setVisibility(View.GONE);
            tab.setCustomView(relativeLayout);
            tab.select();
        }
        viewPager.setCurrentItem(0);
    }

    @Override
    protected void onPause() {
        super.onPause();
        //   unregisterReceiver(catImagereceiver);
    }




    private void addCategoriesIntoDataBase() {
        for (int i = 0; i < categoryList.size(); i++) {
            selectedCategory = categoryList.get(i);
            dbHandler.addCategory(selectedCategory);
            if (selectedCategory.getImageServerPath() != null && !selectedCategory.getImageServerPath().equals(""))
                fileDownload(i);

        }

    }

    private void addProductsIntoDataBase() {
        for (int i = 0; i < productsList.size(); i++) {
            dbHandler.addProduct(productsList.get(i));
            //lptpl1416//dbHandler.addImage(new ImageBean(productsList.get(i).getId(), productsList.get(i).getProductImage(), "PRODUCT", "", productsList.get(i).getProductImage(), productsList.get(i).getId()));

        }
    }

    public void fileDownload(int i) {
        CategoryBean category = categoryList.get(i);
        if (!Util.checkImageIAlreadyExist(category.getCategoryImage())) {
            File direct = new File(Environment.getExternalStorageDirectory()
                    + NetworkConstants.hideFolderFromGallery + "GioneeStar");

            if (!direct.exists()) {
                direct.mkdirs();
            }


            try {
                url = NetworkConstants.BASE_URL + category.getImageServerPath() + "/" + category.getCategoryImage();
                Uri downloadUri = Uri.parse(url);
                DownloadManager.Request request = new DownloadManager.Request(
                        downloadUri);

                request.setAllowedNetworkTypes(
                        DownloadManager.Request.NETWORK_WIFI
                                | DownloadManager.Request.NETWORK_MOBILE)
                        .setAllowedOverRoaming(false)
                        .setTitle("GioneeStar")
                        .setNotificationVisibility(DownloadManager.Request.VISIBILITY_HIDDEN)
                        .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", category.getCategoryImage());

                enqueue = downloadmanager.enqueue(request);
                hm.put(enqueue, i);
                //   enqueue = i;

            } catch (Exception e) {
                e.printStackTrace();
            }
        }
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
        unregisterReceiver(catImagereceiver);
    }

    @Override
    public void onTabChanged(String tabId) {

    }



    private class FetchingDataFromDBAsyncTask extends AsyncTask<String, String, String> {

        @Override
        protected String doInBackground(String... params) {
            categoryList = dbHandler.getAllCategories();
            adapter = new ProductsAdapter(getSupportFragmentManager());
            addCategoryInAdapter();
            return null;
        }

        @Override
        protected void onPostExecute(String s) {
            super.onPostExecute(s);
            if (categoryList.get(0).getImageLocalPath().equals(""))
                Picasso.with(ProductsActivity.this).load(NetworkConstants.BASE_URL + "/" + categoryList.get(0).getImageServerPath() + "/" + categoryList.get(0).getCategoryImage()).into(ivHeader);
            else
                Picasso.with(ProductsActivity.this).load(new File(categoryList.get(0).getImageLocalPath())).into(ivHeader);
            if (adapter != null) {
                setTabLayoutDivider();
            }
        }
    }
}
