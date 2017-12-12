package com.gionee.gioneeabc.activities;

import android.database.Cursor;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.CompareSpecficationAdaptor;
import com.gionee.gioneeabc.bean.CompareSpecficationBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;
import com.google.gson.Gson;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class CompareSpecifictionActivity extends BaseActivity implements AdapterView.OnItemSelectedListener, NetworkTask.Result {


    private static final int GET_DATA = 101;
    AutoCompleteTextView tvGionee, tvNonGionee;
    RecommNonGioneeModelBean recommNonGioneeModelBean;
    private ArrayList<String> nonGionneModelName = new ArrayList<>();
    private ArrayList<String> gionneModelName = new ArrayList<>();
    ArrayList<ProductBean> productBeen;
    private CompareSpecficationAdaptor adaptor;
    private RecyclerView recyclerView;
    private DataBaseHandler dbHandler;
    private ArrayList<CompareSpecficationBean.CompareSubcategory> subcategoryList = new ArrayList<>();
    private int selectedGioneeModel = -1, selectedNonGioneeModel = -1;
    private TextView tvGionneName, tvNonGionneName;
    ImageView ivGionee, ivNonGionee;

    private ImageLoader imageLoader;
    private DisplayImageOptions options;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_compare_specifiction);

        Toolbar toolbar = (Toolbar) findViewById(R.id.tool_bar);
        toolbar.setTitle("Compare");
        toolbar.setTitleTextColor(0xFFFFFFFF);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        final Drawable upArrow = getResources().getDrawable(R.drawable.abc_ic_ab_back_material);
        upArrow.setColorFilter(getResources().getColor(R.color.white), PorterDuff.Mode.SRC_ATOP);
        getSupportActionBar().setHomeAsUpIndicator(upArrow);
        dbHandler = DataBaseHandler.getInstance(this);
        productBeen = dbHandler.getAllProducts();

        init();

        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }

    private void init() {

        imageLoader = ImageLoader.getInstance();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(this)
                .memoryCacheSize(41943040)
                .discCacheSize(104857600)
                .threadPoolSize(10)
                .build();
        imageLoader.init(config);
        options = new DisplayImageOptions.Builder()
                .cacheInMemory(true)
                .cacheOnDisk(true)
                .showStubImage(R.drawable.phone)
                .showImageOnFail(R.drawable.phone)
                .considerExifParams(true)
                .build();

        Cursor cursor = dbHandler.getAllRecommModelData();
        if (cursor.moveToFirst()) {
            String updateCatRes = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            recommNonGioneeModelBean = new Gson().fromJson(updateCatRes, RecommNonGioneeModelBean.class);
            if (recommNonGioneeModelBean != null) {
                for (int i = 0; i < recommNonGioneeModelBean.getData().size(); i++) {
                    for (int j = 0; j < recommNonGioneeModelBean.getData().get(i).getModel().size(); j++) {
                        String name = recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName();
                        nonGionneModelName.add(name);
                    }
                }
            }
        }

        productBeen = dbHandler.getAllProducts();
        if (productBeen != null) {
            for (int i = 0; i < productBeen.size(); i++) {
                String name = productBeen.get(i).getProductName();
                gionneModelName.add(name);
            }
        }

        tvGionneName = (TextView) findViewById(R.id.tv_gionne_name);
        tvNonGionneName = (TextView) findViewById(R.id.tv_nongionne_name);
        ivGionee = (ImageView) findViewById(R.id.iv_gionne);
        ivNonGionee = (ImageView) findViewById(R.id.iv_nongionne);

        tvGionee = (AutoCompleteTextView) findViewById(R.id.tv_gionne);
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_dropdown_item_1line, gionneModelName);
        tvGionee.setThreshold(1);
        tvGionee.setAdapter(adapter);
        tvGionee.setOnItemSelectedListener(this);
        tvGionee.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int postion, long l) {
                Util.hideKeyBoard(CompareSpecifictionActivity.this);
                String name = adapterView.getItemAtPosition(postion).toString();
                for (int i = 0; i < productBeen.size(); i++) {
                    if (productBeen.get(i).getProductName().equalsIgnoreCase(name)) {
                        selectedGioneeModel = productBeen.get(i).getId();
                        tvGionneName.setText(productBeen.get(i).getProductName());
                        if (productBeen.get(i).getProductName() != null && !productBeen.get(i).getProductName().equalsIgnoreCase("")) {
                            String imageUrl = NetworkConstants.BASE_URL + productBeen.get(i).getProductImageServerPath() + "/" + productBeen.get(i).getProductImage();
                            imageLoader.displayImage(imageUrl, ivGionee, options, null);
                        }
                        break;
                    }
                }
                if (selectedNonGioneeModel > 0 && selectedGioneeModel > 0)
                    getDataCompareDataFromServer(selectedGioneeModel, selectedNonGioneeModel);
            }
        });

        tvNonGionee = (AutoCompleteTextView) findViewById(R.id.tv_non_gionne);
        adapter = new ArrayAdapter<String>(this, android.R.layout.simple_dropdown_item_1line, nonGionneModelName);
        tvNonGionee.setThreshold(1);
        tvNonGionee.setAdapter(adapter);
        tvNonGionee.setOnItemSelectedListener(this);
        tvNonGionee.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int poition, long l) {
                Util.hideKeyBoard(CompareSpecifictionActivity.this);
                String name = adapterView.getItemAtPosition(poition).toString();
                for (int i = 0; i < recommNonGioneeModelBean.getData().size(); i++) {
                    for (int j = 0; j < recommNonGioneeModelBean.getData().get(i).getModel().size(); j++) {
                        if (recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName().equalsIgnoreCase(name)) {
                            selectedNonGioneeModel = recommNonGioneeModelBean.getData().get(i).getModel().get(j).getId();
                            tvNonGionneName.setText(recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName());
                            if (recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage() != null
                                    && recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0) != null
                                    && !recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getName().equals("")) {
                                String imageUrl = NetworkConstants.BASE_URL + recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getPath() + "/thumbnail_medium/" + recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getName();
                                imageLoader.displayImage(imageUrl, ivNonGionee, options, null);
                            }
                            break;
                        }
                    }
                }
                if (selectedNonGioneeModel > 0 && selectedGioneeModel > 0)
                    getDataCompareDataFromServer(selectedGioneeModel, selectedNonGioneeModel);
            }
        });

        recyclerView = (RecyclerView) findViewById(R.id.recyclerView);

        recyclerView.setLayoutManager(new LinearLayoutManager(this));

        adaptor = new CompareSpecficationAdaptor(subcategoryList, this);

        recyclerView.setAdapter(adaptor);
        recyclerView.setNestedScrollingEnabled(false);


        if (getIntent().hasExtra("gionee_id")) {//getIntent().hasExtra("gionee_id")
            int id = getIntent().getIntExtra("gionee_id", 0);;//getIntent().getIntExtra("gionee_id", 0);
            for (int i = 0; i < productBeen.size(); i++) {
                if (productBeen.get(i).getId() == (id)) {
                    selectedGioneeModel = productBeen.get(i).getId();
                    tvGionneName.setText(productBeen.get(i).getProductName());
                    tvGionee.setText(productBeen.get(i).getProductName());
                    tvGionee.dismissDropDown();
                    if (productBeen.get(i).getProductName() != null && !productBeen.get(i).getProductName().equalsIgnoreCase("")) {
                        String imageUrl = NetworkConstants.BASE_URL + productBeen.get(i).getProductImageServerPath() + "/" + productBeen.get(i).getProductImage();
                        imageLoader.displayImage(imageUrl, ivGionee, options, null);
                    }
                    break;
                }
            }
        }


    }

    private void getDataCompareDataFromServer(int gionee_model, int other_model) {
        subcategoryList.clear();
        adaptor.notifyDataSetChanged();
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        params.add(new BasicNameValuePair("gionee_model", gionee_model + ""));
        params.add(new BasicNameValuePair("other_model", other_model + ""));
        NetworkTask networkTask = new NetworkTask(CompareSpecifictionActivity.this, GET_DATA, params, null);
        networkTask.exposePostExecute(CompareSpecifictionActivity.this);
        networkTask.execute(NetworkConstants.PHONE_SPECIFICATION_COMP);
    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            switch (id) {
                case GET_DATA:
                    try {
                        JSONObject jsonObject = new JSONObject(object);
                        if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                            CompareSpecficationBean compareSpecficationBean = new Gson().fromJson(object, CompareSpecficationBean.class);
                            if (compareSpecficationBean.getData() != null && compareSpecficationBean.getData().size() > 0) {
                                for (int i = 0; i < compareSpecficationBean.getData().size(); i++) {
                                    String catName = compareSpecficationBean.getData().get(i).getCatName();
                                    for (int j = 0; j < compareSpecficationBean.getData().get(i).getSubcategory().size(); j++) {
                                        CompareSpecficationBean.CompareSubcategory compareSubcategory = compareSpecficationBean.getData().get(i).getSubcategory().get(j);
                                        if (!catName.equalsIgnoreCase("")) {
                                            compareSubcategory.setCatName(catName);
                                            catName = "";
                                        }
                                        subcategoryList.add(compareSubcategory);
                                    }
                                }
                                adaptor.notifyDataSetChanged();
                            }
                        }
                    } catch (Exception ex) {

                    }
                    break;
            }
        }
    }

    @Override
    public void onItemSelected(AdapterView<?> arg0, View arg1, int position,
                               long arg3) {
        // TODO Auto-generated method stub
        //Log.d("AutocompleteContacts", "onItemSelected() position " + position);
    }

    @Override
    public void onNothingSelected(AdapterView<?> arg0) {
        // TODO Auto-generated method stub

        InputMethodManager imm = (InputMethodManager) getSystemService(
                INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(getCurrentFocus().getWindowToken(), 0);

    }

}
