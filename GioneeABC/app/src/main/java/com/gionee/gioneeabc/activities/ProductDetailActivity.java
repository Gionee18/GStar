package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.os.Bundle;
import android.os.Environment;
import android.support.design.widget.AppBarLayout;
import android.support.design.widget.CollapsingToolbarLayout;
import android.support.design.widget.TabLayout;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.ImagesAdapter;
import com.gionee.gioneeabc.adapters.ProductsAdapter;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.ProductOverviewFragment;
import com.gionee.gioneeabc.fragments.ProductSpecificationFragment;
import com.gionee.gioneeabc.fragments.ProductVaultFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

import me.relex.circleindicator.CircleIndicator;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class ProductDetailActivity extends BaseActivity implements NetworkTask.Result {
    Toolbar toolbar;
    ViewPager imagesViewPager, fragmentsViewPager;
    TabLayout tabLayout;
    Window window;
    CollapsingToolbarLayout collapsingToolbarLayout;
    AppBarLayout appBarLayout;
    public ProductBean product;
    ProductsAdapter productsAdapter;
    DataBaseHandler dbHandler;
    public List<DocumentBean> documentList;
    List<ImageBean> imageList;
    NetworkTask networkTask;
    private final int GET_DOC = 101;
    ImagesAdapter adapter = null;
    CircleIndicator ipIndicator;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.product_detail_screen);
        product = (ProductBean) getIntent().getSerializableExtra("product");
        dbHandler = DataBaseHandler.getInstance(ProductDetailActivity.this);
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        toolbar.setTitle(product.getProductName());

        GStarApplication.getInstance().trackScreenView("Product Detail Screen");

        setSupportActionBar(toolbar);
        toolbar.setOverflowIcon(getResources().getDrawable(R.drawable.dots));

        ((ImageView)findViewById(R.id.back)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent();
                intent.putExtra("clean", false);
                setResult(101, intent);
                finish();
            }
        });

        ipIndicator = (CircleIndicator) findViewById(R.id.indicator);
        window = ProductDetailActivity.this.getWindow();
        appBarLayout = (AppBarLayout) findViewById(R.id.appBarLayout);

/* Bitmap bitmap = BitmapFactory.decodeResource(getResources(),
                imagesList[0]);*/

        collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);

  /*      Palette.from(bitmap).generate(new Palette.PaletteAsyncListener() {
            @SuppressWarnings("ResourceType")
            @Override
            public void onGenerated(Palette palette) {

                int vibrantColor = palette.getVibrantColor(R.color.colorPrimary);
                int vibrantDarkColor = palette.getDarkVibrantColor(R.color.colorPrimary);
                collapsingToolbarLayout.setContentScrimColor(vibrantColor);
                collapsingToolbarLayout.setStatusBarScrimColor(vibrantDarkColor);
                window.setStatusBarColor(vibrantColor);
                appBarLayout.setBackgroundColor(vibrantColor);
            }
        });
*/
// clear FLAG_TRANSLUCENT_STATUS flag:
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

// add FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS flag to the window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

//        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                Intent intent = new Intent();
//                intent.putExtra("clean", false);
//                setResult(101, intent);
//                finish();
//            }
//        });
        imagesViewPager = (ViewPager) findViewById(R.id.imagesViewPager);

/*
        imagesViewPager.setOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int position) {
                Bitmap bitmap = BitmapFactory.decodeResource(getResources(),
                        imagesList[position]);


                Palette.from(bitmap).generate(new Palette.PaletteAsyncListener() {
                    @SuppressWarnings("ResourceType")
                    @Override
                    public void onGenerated(Palette palette) {

                        int vibrantColor = palette.getVibrantColor(R.color.colorPrimary);
                        int vibrantDarkColor = palette.getDarkVibrantColor(R.color.colorPrimary);
                        collapsingToolbarLayout.setContentScrimColor(vibrantColor);
                        collapsingToolbarLayout.setStatusBarScrimColor(vibrantDarkColor);
                        window.setStatusBarColor(vibrantColor);
                        appBarLayout.setBackgroundColor(vibrantColor);

                    }
                });

            }

            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });
*/
        if (adapter != null) {
            imagesViewPager.setAdapter(adapter);
            ipIndicator.setViewPager(imagesViewPager);
        }
        fragmentsViewPager = (ViewPager) findViewById(R.id.viewPager);
        fragmentsViewPager.setPageMargin(5); // TODO Convert 'px' to 'dp'
        fragmentsViewPager.setPageMarginDrawable(R.color.black);
        setupViewPager(fragmentsViewPager);
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        tabLayout.setupWithViewPager(fragmentsViewPager);
        setTabLayoutDivider();
        // getDataFromServer();
        getDataFromLocal(product.getProductImagesJson());
        /*if (dbHandler.getDocumentsByProductId(product.getId()) == null) {
            if (Util.isNetworkAvailable(this))
                getDataFromServer();
        } else {
            getDataFromLocal();
           // getDataFromServer();
        }*/

    }


    private void getDataFromServer() {

        ArrayList<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("product_id", "" + product.getId()));
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));

        networkTask = new NetworkTask(ProductDetailActivity.this, GET_DOC, params);
        networkTask.exposePostExecute(ProductDetailActivity.this);
        networkTask.execute(NetworkConstants.GET_PRODUCTS_DETAIL_URL);
    }

    private void getDataFromLocal(String json) {
        JSONArray child1 = null;
        imageList = new ArrayList<>();
        try {
            if (json != null && !json.equals("")) {
                child1 = new JSONArray(json);
                for (int k = 0; k < child1.length(); k++) {
                    ImageBean imageBean = new ImageBean();
                    JSONObject asset = child1.getJSONObject(k);
                    imageBean.setImageId(asset.optInt("image_id"));
                    imageBean.setImageServerPath(asset.optString("path"));
                    imageBean.setImageName(asset.optString("name"));
                    imageBean.setImageLocalPath(Environment.getExternalStorageDirectory()
                            + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + imageBean.getImageName());
                    imageList.add(imageBean);
                }
                adapter = new ImagesAdapter(this, imageList, false, true);
                imagesViewPager.setAdapter(adapter);
                ipIndicator.setViewPager(imagesViewPager);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

    }


    private void setupImageViewPager(ViewPager viewPager) {
        adapter = new ImagesAdapter(this, imageList);
        viewPager.setAdapter(adapter);
        ipIndicator.setViewPager(viewPager);
    }

    private void setupViewPager(ViewPager viewPager) {
        productsAdapter = new ProductsAdapter(getSupportFragmentManager());
        productsAdapter.addFragment(new ProductOverviewFragment(), "OVERVIEW");
        //  productsAdapter.addFragment(new ProductDescriptionFragment(), "DESCRIPTION");
        productsAdapter.addFragment(new ProductSpecificationFragment(), "SPECIFICATIONS");
        productsAdapter.addFragment(new ProductVaultFragment(), "VAULT");
        viewPager.setAdapter(productsAdapter);
    }

    void setTabLayoutDivider() {
        fragmentsViewPager.setAdapter(productsAdapter);
        tabLayout.setupWithViewPager(fragmentsViewPager);
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
        fragmentsViewPager.setCurrentItem(0);
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent();
        intent.putExtra("clean", false);
        setResult(101, intent);
        finish();
    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            try {
                JSONObject main = new JSONObject(object);
                documentList = new ArrayList<DocumentBean>();
                DocumentBean document = null;


                if (main.has("error")) {

                    Util.createToast(this, "Session expire, please login again");
                    DataStore.setLoggedIn(this, false);
                    DataStore.setProfilePic(this, "");

                    startActivity(new Intent(ProductDetailActivity.this, LoginActivity.class));
                    finish();

                } else if (main.getString("status").equalsIgnoreCase("success")) {
                    JSONArray mainArray = main.optJSONArray("data");
                    JSONObject data = mainArray.getJSONObject(0);
                    JSONArray productImage = data.optJSONArray("pro_image");
                    imageList = new ArrayList<ImageBean>();
                    if (productImage != null) {
                        for (int i = 0; i < productImage.length(); i++) {
                            ImageBean img = new ImageBean();
                            JSONObject product = productImage.getJSONObject(i);
                            img.setImageId(product.optInt("image_id"));
                            img.setImageName(product.optString("image_name"));
                            img.setImageLocalPath("");
                            img.setImageServerPath(product.optString("image_path"));
                            img.setImageTitle(product.optString("image_title"));
                            imageList.add(img);
                        }
                    }

                    JSONArray productAsset = data.optJSONArray("pro_doc");
                    if (productAsset != null) {
                        for (int i = 0; i < productAsset.length(); i++) {
                            JSONObject child = productAsset.getJSONObject(i);
                            document = new DocumentBean();

                            document.setDocId(child.optInt("doc_id"));
                            document.setDocLocalPath("");
                            document.setDocTitle(child.optString("doc_title"));

                            document.setDocUrl(child.optString("doc_path"));
                            String docName = child.optString("doc_name");
                            document.setDocName(docName);
                            if (docName != null && !docName.equals("")) {
                                String[] docTypeArray = docName.split(Pattern.quote("."));

                                document.setDocType(docTypeArray[docTypeArray.length - 1].toUpperCase());

                            } else
                                document.setDocType("OTHER");
                            document.setProductId(product.getId());

                            documentList.add(document);


                        }
                    }
                    if (imageList.size() > 0) {

                        for (int k = 0; k < imageList.size(); k++) {
                            document = new DocumentBean();
                            ImageBean imageBean = imageList.get(k);
                            document.setDocId(imageBean.getImageId());
                            document.setDocLocalPath("");
                            document.setDocTitle(imageBean.getImageTitle());
                            document.setDocUrl(imageBean.getImageServerPath());
                            String docName = imageBean.getImageName();
                            document.setDocName(docName);
                            if (docName != null && !docName.equals("")) {
                                String[] docTypeArray = docName.split(Pattern.quote("."));

                                document.setDocType(docTypeArray[docTypeArray.length - 1].toUpperCase());

                            } else
                                document.setDocType("OTHER");
                            document.setProductId(product.getId());

                            documentList.add(document);
                        }

                    }


                    if (imageList.size() > 0)
                        setupImageViewPager(imagesViewPager);

                } else if (main.getString("status").equalsIgnoreCase("error")) {

                }

            } catch (JSONException e) {
                e.printStackTrace();
            }


        }

    }

    /*@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        super.onCreateOptionsMenu(menu);
        new MenuInflater(getApplication()).inflate(R.menu.menu_recommender, menu);
        if (getIntent().hasExtra("product_from")) {
            if (getIntent().getStringExtra("product_from").equalsIgnoreCase("recomm")) {
                if (menu != null) {
                    menu.findItem(R.id.compare).setVisible(true);
                }else {
                    menu.findItem(R.id.compare).setVisible(false);
                }
            }else {
                menu.findItem(R.id.compare).setVisible(false);
            }
        }else {
            menu.findItem(R.id.compare).setVisible(false);
        }
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.compare) {
            Intent intent = new Intent(this, CompareSpecifictionActivity.class);
            intent.putExtra("gionee_id",product.getId());
            startActivity(intent);
        }
        return super.onOptionsItemSelected(item);
    }*/
}
