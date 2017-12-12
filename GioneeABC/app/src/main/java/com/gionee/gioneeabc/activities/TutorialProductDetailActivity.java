package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.os.Bundle;
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
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.TutorialProductDetailsFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;
import com.squareup.picasso.Picasso;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

import me.relex.circleindicator.CircleIndicator;

/**
 * Created by root on 4/10/16.
 */
public class TutorialProductDetailActivity extends BaseActivity implements NetworkTask.Result {
    private Toolbar toolbar;
    private ViewPager imagesViewPager, fragmentsViewPager;
    private TabLayout tabLayout;
    private Window window;
    private CollapsingToolbarLayout collapsingToolbarLayout;
    private AppBarLayout appBarLayout;
    public TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean product;
    private ProductsAdapter productsAdapter;
    private DataBaseHandler dbHandler;
    public List<DocumentBean> documentList;
    private List<ImageBean> imageList;
    private NetworkTask networkTask;
    private ImageView ivHeader;
    private final int GET_DOC = 101;
    private ImagesAdapter adapter = null;
    private CircleIndicator ipIndicator;
    private int tutorialPageSelected;
    public static int pos;
    private TutorialResponseBean tutorialResponseBean;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.tutorial_product_detail_screen);
        initIds();
        tutorialPageSelected = TutorialProductsActivity.selectedPage;
        pos = getIntent().getIntExtra("product", 0);
        getProduct();

        // product = (TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean) getIntent().getStringExtra("product");
        //  dbHandler = DataBaseHandler.getInstance(TutorialProductDetailActivity.this);
        toolbar.setTitle(product.getProduct_name());
        GStarApplication.getInstance().trackScreenView("Tutorial Detail Screen");
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        ipIndicator = (CircleIndicator) findViewById(R.id.indicator);
        window = TutorialProductDetailActivity.this.getWindow();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

// add FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS flag to the window
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

        /*if (adapter != null) {
            imagesViewPager.setAdapter(adapter);
            ipIndicator.setViewPager(imagesViewPager);
        }*/
        setupViewPager(fragmentsViewPager);
        /*tabLayout.setupWithViewPager(fragmentsViewPager);
        setTabLayoutDivider();
        getDataFromServer();*/
        //getDataFromLocal(product.getPro_image().toString());


    }

    private void getProduct() {
        tutorialResponseBean = TutorialProductsActivity.tutorialDataCatogaryBean;
        product = tutorialResponseBean.getData().get(tutorialPageSelected).getProduct().get(pos);
    }

    private void initIds() {
        toolbar = (Toolbar) findViewById(R.id.tool_bar);
        ivHeader = (ImageView) findViewById(R.id.ivHeader);
        appBarLayout = (AppBarLayout) findViewById(R.id.appBarLayout);
        collapsingToolbarLayout = (CollapsingToolbarLayout) findViewById(R.id.htab_collapse_toolbar);
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        fragmentsViewPager = (ViewPager) findViewById(R.id.viewPager);
        imagesViewPager = (ViewPager) findViewById(R.id.imagesViewPager);
    }

    private void getDataFromServer() {
        try {
            if (product.getPro_image().size() > 0)
                Picasso.with(this).load(NetworkConstants.BASE_URL + "/" + product.getPro_image().get(0).getPath() + "/" + product.getPro_image().get(0).getName()).into(ivHeader);
            else
                ivHeader.setImageResource(R.color.colorPrimary);
        }catch (Exception e){
            ivHeader.setImageResource(R.color.colorPrimary);
        }
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
                    imageBean.setImageLocalPath(asset.optString("path"));
                    imageBean.setImageName(asset.optString("name"));
                    imageBean.setImageTitle(asset.optString("title"));
                    /*imageBean.setImageLocalPath(Environment.getExternalStorageDirectory()
                            + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + imageBean.getImageName());*/
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
        //productsAdapter.addFragment(new ProductOverviewFragment(), "OVERVIEW");
        //  productsAdapter.addFragment(new ProductDescriptionFragment(), "DESCRIPTION");
        //productsAdapter.addFragment(new ProductSpecificationFragment(), "SPECIFICATION");
        productsAdapter.addFragment(new TutorialProductDetailsFragment(), product.getProduct_name().toString());
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

                    startActivity(new Intent(TutorialProductDetailActivity.this, LoginActivity.class));
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


}
