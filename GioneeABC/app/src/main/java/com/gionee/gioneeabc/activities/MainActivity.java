package com.gionee.gioneeabc.activities;

import android.animation.ValueAnimator;
import android.app.Dialog;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBar;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.View;
import android.view.Window;
import android.view.animation.DecelerateInterpolator;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.NavigationDrawerAdapter;
import com.gionee.gioneeabc.bean.CategoryBean;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.HomeFragment;
import com.gionee.gioneeabc.helpers.AsynchTaskImageDownload;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;
import com.gionee.gioneeabc.interfaces.IMessenger;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;


public class MainActivity extends AppCompatActivity implements IMessenger, NetworkTask.Result {
    private static final int GET_USER_INFO = 108;
    public static DrawerLayout drawerLayout;
    public static ActionBar actionBar;
    static ActionBarDrawerToggle actionBarDrawerToggle;
    List<ImageBean> downloadEachImagesList;
    private CharSequence mTitle;
    public static Toolbar toolBar;
    RecyclerView recyclerView;
    private static final float MENU_POSITION = 0f;
    private static final float ARROW_POSITION = 1.0f;
    public static FragmentManager fragmentManager;
    public static FragmentTransaction transaction;
    public static String[] drawerElements = {"Home", "Products", "Recommender", "Tutorials", "Updates", "Edit Profile", "Change Password", "FAQs", "Logout"};
    public static String[] drawerElementsIcons = {"l", "g", "h", "i", "k", "x", "f", "o", "b"};
    public static int[] drawerElementsIconsBack = {
            R.color.orange,
            R.color.cyan,
            R.color.orange,
            R.color.pink,
            R.color.cyan,
            R.color.green,
            R.color.teal,
            R.color.green,
            R.color.red};
    NetworkTask networkTask;
    public static UserBean user;
    public static RecyclerView.Adapter adapter;
    DataBaseHandler database;
    public List<CategoryBean> categoryList;
    public List<ProductBean> productsList;
    //  public List<ProductBean> newProductsList;
    private final int GET_CATEGORY = 101;
    private final int USER_ACTIVATION = 103;
    private final int CHECK_UPDATE = 102, GET_TUTORIAL = 104, GET_UPDATE_CATEGORY = 105;
    private static final int GET_RECOMM_MODEL = 106;
    private static final int GET_RECOMM_ATTRIB = 107;
    private DataBaseHandler dbHandler;
    private UnRegisterReceiver unRegisterReceiver;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        fragmentManager = getSupportFragmentManager();
        toolBar = (Toolbar) findViewById(R.id.tool_bar);
//        toolBar.setTitle(getString(R.string.app_name));
        toolBar.setTitle("");
        setSupportActionBar(toolBar);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        TextView tvTitle = (TextView) toolBar.findViewById(R.id.tv_toolbar_title);
        tvTitle.setText(getString(R.string.app_name));
        TextView tvUpdate = (TextView) toolBar.findViewById(R.id.tv_update);
        tvUpdate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkUpdateFromServer();
            }
        });

        database = DataBaseHandler.getInstance(MainActivity.this);

        user = (UserBean) getIntent().getSerializableExtra("user");
        if (user != null)
            database.addUser(user);

        drawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
        recyclerView = (RecyclerView) findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        downloadEachImagesList = new ArrayList<>();
        adapter = new NavigationDrawerAdapter(this, drawerElements, drawerElementsIcons, drawerElementsIconsBack);
        unRegisterReceiver = (UnRegisterReceiver) adapter;
        recyclerView.setAdapter(adapter);
        dbHandler = DataBaseHandler.getInstance(this);
        categoryList = new ArrayList<CategoryBean>();
        productsList = new ArrayList<ProductBean>();

        actionBarDrawerToggle = new ActionBarDrawerToggle(MainActivity.this, drawerLayout, toolBar, R.string.openDrawer, R.string.closeDrawer) {
            @Override
            public void onDrawerClosed(View drawerView) {
                super.onDrawerClosed(drawerView);
                invalidateOptionsMenu();

            }

            @Override
            public void onDrawerOpened(View drawerView) {
                super.onDrawerOpened(drawerView);
                invalidateOptionsMenu();
            }
        };
        if (Util.isNetworkAvailable(this)) {
            if (DataStore.getUpdatedDate(this).equals("0")) {
                getCategoriesFromServer();
                onHitGetTutorialWebService();
                getUpdateCategoryDataFromServer();
                onHitGetRecommenderAttribData();
                onHitGetRecommenderModelData();
            }
        } else {
            getCategoriesFromLocal();
        }
        if (user != null && user.getStatus().toString().equalsIgnoreCase("0")) {
            openProfileActivationDiaqlog();
        }
        drawerLayout.setDrawerListener(actionBarDrawerToggle);
        actionBarDrawerToggle.setDrawerIndicatorEnabled(true);
        actionBarDrawerToggle.syncState();
        Intent intent = getIntent();
        if (intent.hasExtra("type") && intent.getStringExtra("type").toString().equalsIgnoreCase("push")) {
            if (intent.getStringExtra("NavigationActivity").toString().equalsIgnoreCase("UpdateActivity")) {
                getUpdateCategoryDataFromServer();
            }
        }

        Cursor cursor = DataBaseHandler.getInstance(this).getAllTutorialCategory();
        if (cursor.getCount() <= 0) {
            onHitGetTutorialWebService();
        }
    }

    private void getUserData() {
        user = dbHandler.getUser();
        networkTask = new NetworkTask(this, GET_USER_INFO, null, null);
        networkTask.exposePostExecute(this);
        networkTask.execute(NetworkConstants.USER_DATA + user.getUserId() + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN));
    }

    private void onHitGetTutorialWebService() {
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        networkTask = new NetworkTask(MainActivity.this, GET_TUTORIAL, params, null);
        networkTask.exposePostExecute(MainActivity.this);
        networkTask.execute(NetworkConstants.GET_TUTORIALS);
    }

    @Override
    public boolean onMenuOpened(int featureId, Menu menu) {
        if (drawerLayout.isDrawerOpen(GravityCompat.START))
            drawerLayout.closeDrawers();
        return super.onMenuOpened(featureId, menu);

    }

    private void onHitGetRecommenderModelData() {
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        networkTask = new NetworkTask(MainActivity.this, GET_RECOMM_MODEL, params, null);
        networkTask.exposePostExecute(MainActivity.this);
        networkTask.execute(NetworkConstants.GET_RECOMM_MODEL_DATA);
    }

    private void onHitGetRecommenderAttribData() {
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        networkTask = new NetworkTask(MainActivity.this, GET_RECOMM_ATTRIB, params, null);
        networkTask.exposePostExecute(MainActivity.this);
        networkTask.execute(NetworkConstants.GET_RECOMM_ATTRIB_DATA);
    }

    private void getUpdateCategoryDataFromServer() {

        ArrayList<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));

        networkTask = new NetworkTask(MainActivity.this, GET_UPDATE_CATEGORY, params);
        networkTask.exposePostExecute(MainActivity.this);
        networkTask.execute(NetworkConstants.CATEGORY_UPDATE_URL);
    }

    private void openProfileActivationDiaqlog() {
        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_validate_user_data);
        dialog.show();
        dialog.setCancelable(false);
        TextView tvRequest = (TextView) dialog.findViewById(R.id.tv_request);
        tvRequest.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (Util.isNetworkAvailable(MainActivity.this)) {
                    dialog.dismiss();
                    networkTask = new NetworkTask(MainActivity.this, USER_ACTIVATION, null, null);
                    networkTask.exposePostExecute(MainActivity.this);
                    networkTask.execute(NetworkConstants.USER_ACTIVATION_REQUEST + "?access_token=" + DataStore.getAuthToken(MainActivity.this, DataStore.AUTH_TOKEN));
                } else {
                    Util.createToast(MainActivity.this, "Please Check Your Internet Connection");
                }
            }
        });
        TextView tvCancel = (TextView) dialog.findViewById(R.id.tv_cancel);
        tvCancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dialog.dismiss();
                Util.logout(MainActivity.this);
            }
        });


    }

    private void getCategoriesFromServer() {

        networkTask = new NetworkTask(MainActivity.this, GET_CATEGORY, null, null);
        networkTask.exposePostExecute(this);
        networkTask.execute(NetworkConstants.UPDATE_URL + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN) + "&last_updated_date=" + DataStore.getUpdatedDate(this).replace(" ", "%20"));
    }

    private void checkUpdateFromServer() {

        networkTask = new NetworkTask(MainActivity.this, CHECK_UPDATE, null, null);
        networkTask.exposePostExecute(this);
        networkTask.execute(NetworkConstants.UPDATE_COUNT_URL + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN) + "&last_updated_date=" + DataStore.getUpdatedDate(this).replace(" ", "%20"));
    }

    private void getCategoriesFromLocal() {
        categoryList = dbHandler.getAllCategories();
    }

    public static void animateToBackArrow() {
        ValueAnimator anim = ValueAnimator.ofFloat(MENU_POSITION, ARROW_POSITION);
        anim.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator valueAnimator) {
                float slideOffset = (Float) valueAnimator.getAnimatedValue();
                actionBarDrawerToggle.onDrawerSlide(drawerLayout, slideOffset);
            }
        });

        anim.setInterpolator(new DecelerateInterpolator());
        anim.start();
    }

    public static void animateToMenu() {
        ValueAnimator anim = ValueAnimator.ofFloat(ARROW_POSITION, MENU_POSITION);
        anim.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator valueAnimator) {
                float slideOffset = (Float) valueAnimator.getAnimatedValue();
                actionBarDrawerToggle.onDrawerSlide(drawerLayout, slideOffset);
            }
        });

        anim.setInterpolator(new DecelerateInterpolator());
        anim.start();
    }


    @Override
    public void onSendMessage(Fragment fr, Bundle b) {

        fragmentManager = getSupportFragmentManager();
        fr.setArguments(b);

        transaction = fragmentManager.beginTransaction();
        transaction.setTransition(FragmentTransaction.TRANSIT_FRAGMENT_OPEN);
        transaction.replace(R.id.container, fr);
        transaction.addToBackStack(null);
        transaction.commit();
    }

    @Override
    public void onBackPressed() {
        if (drawerLayout.isDrawerOpen(GravityCompat.START))
            drawerLayout.closeDrawers();
        else {
            super.onBackPressed();
        }
    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            if (id == GET_USER_INFO) {
                try {
                    JSONObject main = new JSONObject(object);
                    if (main.has("status") && main.optString("status").equals("success")) {
                        JSONObject data = main.optJSONObject("data");
                        String firstName = data.optString("first_name");
                        String lastName = data.optString("last_name");
                        String etEmail = data.optString("email");
                        dbHandler.updateUserName(user.getUserId(), firstName + " " + lastName, etEmail);
                        dbHandler.addUserProfileImageServerUrl(user.getUserId(), data.optString("profile_picture"), "");
                        if (adapter != null)
                            adapter.notifyDataSetChanged();
                    }
                } catch (Exception e) {
                }
            } else if (id == GET_RECOMM_MODEL) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    DataBaseHandler.getInstance(this).deleteAllRecommModelData();
                    if (jsonObject.has("error")) {
                        Util.logoutwithMessage(this);
                    } else {
                        if (!(jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                            Util.createToast(this, jsonObject.optString("msg"));
                        } else {
                            DataBaseHandler.getInstance(this).addGetData(object, DataBaseHandler.TYPE_RECOMM_MODEL);
                        }
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            } else if (id == GET_RECOMM_ATTRIB) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    DataBaseHandler.getInstance(this).deleteAllRecommAttribData();
                    if (jsonObject.has("error")) {
                        Util.logoutwithMessage(this);
                    } else {
                        if (!(jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                            Util.createToast(this, jsonObject.optString("msg"));
                        } else {
                            DataBaseHandler.getInstance(this).addGetData(object, DataBaseHandler.TYPE_RECOMM_ATTRIB);
                        }
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            } else if (id == GET_TUTORIAL) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    DataBaseHandler.getInstance(this).deleteAllTutorialCategory();
                    if (jsonObject.has("error")) {
                        Util.logoutwithMessage(this);
                    } else {
                        if (!(jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                            Util.createToast(this, getString(R.string.tutorials_not_found));
                        } else {
                            DataBaseHandler.getInstance(this).addGetData(object, DataBaseHandler.TYPE_TUTORIAL_CATEGORY);
                        }
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            } else if (id == GET_UPDATE_CATEGORY) {

                try {
                    JSONObject jsonObject = new JSONObject(object);
                    DataBaseHandler.getInstance(this).deleteAllUpdateCategory();
                    if (jsonObject.has("error")) {
                        Util.logoutwithMessage(this);
                    } else {
                        if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                            DataBaseHandler.getInstance(this).addGetData(object, DataBaseHandler.TYPE_UPDATE_CATEGORY);
                            Intent intent = getIntent();
                            if (intent.hasExtra("type") && intent.getStringExtra("type").toString().equalsIgnoreCase("push")) {
                                if (intent.getStringExtra("NavigationActivity").toString().equalsIgnoreCase("UpdateActivity")) {
                                    Intent i = new Intent(this, UpdateActivity.class);
                                    i.putExtra("type", "push");
                                    i.putExtra("topic", intent.getStringExtra("topic"));
                                    i.putExtra("category", intent.getStringExtra("category"));
                                    i.putExtra("subcategory", intent.getStringExtra("subcategory"));
                                    startActivity(i);
                                }
                            }
                        }
                    }

                } catch (JSONException e) {
                    e.printStackTrace();
                }


            } else if (id == USER_ACTIVATION) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if (jsonObject.has("status") && jsonObject.optString("status").equalsIgnoreCase("success")) {
                        if (!jsonObject.optString("msg").isEmpty()) {
                            Util.createToast(MainActivity.this, jsonObject.optString("msg"));
                        } else
                            Util.createToast(MainActivity.this, "Reactivation request sent successfully");
                        Util.logout(this);
                    } else {
                        Util.createToast(MainActivity.this, "Something went wrong please try again later");
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }


            } else if (id == GET_CATEGORY) {
                try {
                    JSONObject main = new JSONObject(object);
                    CategoryBean category = null;
                    ProductBean product;
                    if (main.has("error")) {

                        Util.logoutwithMessage(this);

                    } else if (main.getString("status").equalsIgnoreCase("success")) {
                        JSONArray mainArray = null;
                        JSONObject mainObject = null;
                        DataStore.setUpdatedDate(this, main.optString("time_flag"));
                        mainObject = main.optJSONObject("data");
                        if (mainObject != null) {
                            if (mainObject.has("categories")) {
                                mainArray = mainObject.optJSONArray("categories");
                                for (int i = 0; i < mainArray.length(); i++) {
                                    JSONObject child = mainArray.getJSONObject(i);
                                    if (child.optString("status") != null && !child.optString("status").isEmpty() && child.optString("status").equalsIgnoreCase("1")) {
                                        category = new CategoryBean();
                                        //JSONObject categoryObject = child.getJSONObject("description");
                                        category.setCategoryId(child.optInt("id"));
                                        category.setCategoryParentId(child.optInt("category_parent_id"));
                                        category.setCategoryName(child.optString("category_name"));
                                        category.setCategoryPosition(child.optInt("position"));
                                        category.setCategoryDesc(child.optString("description"));

                                        if (child.has("cat_image")) {
                                            JSONArray assetArray = child.getJSONArray("cat_image");
                                            for (int j = 0; j < assetArray.length(); j++) {
                                                JSONObject asset = assetArray.getJSONObject(j);
                                                category.setImageId(asset.optInt("image_id"));
                                                category.setImageServerPath(asset.optString("path"));
                                                category.setImageLocalPath("");
                                                category.setCategoryImage(asset.optString("name"));
                                            }
                                        } else {
                                            category.setImageId(0);
                                            category.setImageServerPath("");
                                            category.setImageLocalPath("");
                                            category.setCategoryImage("");
                                        }
                                        categoryList.add(category);
                                    } else {
                                        if (dbHandler.checkCategoryIsExist(child.optInt("id"))) {
                                            for (int j = 0; j < categoryList.size(); j++) {
                                                if (categoryList.get(j).getCategoryId() == child.optInt("id")) {
                                                    categoryList.remove(j);
                                                    break;
                                                }
                                            }
                                        }
                                        dbHandler.deleteCategoryById(child.optInt("id"));
                                    }
                                }
                            }

                            if (mainObject.has("products")) {
                                JSONArray productsArray = mainObject.getJSONArray("products");
                                for (int j = 0; j < productsArray.length(); j++) {
                                    JSONObject productObject = productsArray.getJSONObject(j);
                                    if (productObject.optString("status") != null && !productObject.optString("status").isEmpty() && productObject.optString("status").equalsIgnoreCase("1")) {
                                        product = new ProductBean();
                                        product.setId(productObject.optInt("product_id"));
                                        product.setImageId(productObject.optInt("image_id"));
                                        product.setCategoryId(productObject.optInt("category_id"));
                                        product.setProductName(productObject.optString("product_name"));
                                        product.setProductDesc(productObject.optString("desc1"));
                                        product.setIsNewProduct(productObject.optString("new_product_flag"));
                                        product.setProductDesc1(productObject.optString("desc3"));
                                        product.setProductDesc2(productObject.optString("desc1"));
                                        product.setLaunch_date(productObject.optString("launch_date"));
                                        if (productObject.has("pro_doc"))
                                            product.setVaultDocsJson(productObject.getJSONArray("pro_doc").toString());
                                        if (productObject.has("pro_image")) {
                                            product.setProductImagesJson(productObject.getJSONArray("pro_image").toString());
                                            JSONArray productAsset = productObject.getJSONArray("pro_image");
                                            for (int k = 0; k < productAsset.length(); k++) {
                                                JSONObject asset = productAsset.getJSONObject(0);
                                                product.setImageId(asset.optInt("image_id"));
                                                product.setProductImageServerPath(asset.optString("path") + "/thumbnail");
                                                product.setProductImageLocalPath("");
                                                product.setProductImage(asset.optString("name"));

                                                JSONObject asset2 = productAsset.getJSONObject(k);
                                                ImageBean img = new ImageBean();
                                                img.setImageId(asset2.optInt("image_id"));
                                                img.setImageName(asset2.optString("name"));
                                                img.setImageServerPath(asset2.optString("path") + "/thumbnail");
                                                downloadEachImagesList.add(img);
                                            }
                                        } else {
                                            product.setImageId(0);
                                            product.setProductImageServerPath("");
                                            product.setProductImageLocalPath("");
                                            product.setProductImage(productObject.optString(""));
                                        }
                                        productsList.add(product);
                                    } else {
                                        if (dbHandler.checkProductIsExist(productObject.optInt("product_id"))) {
                                            for (int k = 0; k < productsList.size(); k++) {
                                                if (productsList.get(k).getId() == productObject.optInt("product_id")) {
                                                    productsList.remove(k);
                                                    break;
                                                }
                                            }
                                        }
                                        dbHandler.deleteProductById(productObject.optInt("product_id"));
                                    }
                                }
                            }
                            if (mainObject.has("deleteLog")) {
                                JSONObject jsonObject = mainObject.getJSONObject("deleteLog");
                                if (jsonObject.has("category")) {
                                    JSONArray jsonArray = jsonObject.getJSONArray("category");
                                    for (int i = 0; i < jsonArray.length(); i++) {
                                        dbHandler.deleteCategoryById(jsonArray.getInt(i));
                                        for (int j = 0; j < categoryList.size(); j++) {
                                            if (categoryList.get(j).getCategoryId() == jsonArray.getInt(i)) {
                                                categoryList.remove(j);
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (jsonObject.has("product")) {
                                    JSONArray jsonArray = jsonObject.getJSONArray("product");
                                    for (int i = 0; i < jsonArray.length(); i++) {
                                        dbHandler.deleteProductById(jsonArray.getInt(i));
                                        for (int j = 0; j < productsList.size(); j++) {
                                            if (productsList.get(j).getId() == jsonArray.getInt(i)) {
                                                productsList.remove(j);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    } else if (main.getString("status").equalsIgnoreCase("error")) {

                        Util.logoutwithMessage(this);
                    }
                    if (categoryList.size() > 0) {
                        addCategoriesIntoDataBase();
                        addNewCategoryInDb();
                    }
                    if (productsList.size() > 0)
                        addProductsIntoDataBase();
                    new AsynchTaskImageDownload(this, downloadEachImagesList).execute();
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            } else if (id == CHECK_UPDATE) {
                try {
                    JSONObject main = new JSONObject(object);
                    if (main.has("error")) {

                        Util.createToast(this, "Session expire, please login again");
                        DataStore.setLoggedIn(this, false);
                        DataStore.setProfilePic(this, "");
                        startActivity(new Intent(this, LoginActivity.class));
                        finish();

                    } else if (main.getString("status").equalsIgnoreCase("success")) {
                        JSONArray mainArray = null;
                        JSONObject mainObject = null;

                        if (!appNeedtoUpdate(main.optInt("count"))) {
                            getCategoriesFromLocal();
                            return;
                        }
                        DataStore.setUpdatedDate(this, main.optString("time_flag"));
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        } else {
            Util.createToast(this, "Data not found Please try again");
        }
    }

    private void deleteTables() {
        dbHandler.deleteCategoryData();
        dbHandler.deleteProductData();
        dbHandler.deleteNewProductData();
    }

    private boolean appNeedtoUpdate(int count) {
        if (count > 0) {
            getCategoriesFromServer();
            onHitGetTutorialWebService();
            getUpdateCategoryDataFromServer();
            onHitGetRecommenderAttribData();
            onHitGetRecommenderModelData();
            Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.container);
            if (currentFragment instanceof HomeFragment) {
                ((HomeFragment) currentFragment).getDashboardImagesFromServer();
            }
            getUserData();
        }
        return count > 0;
    }

    private void addCategoriesIntoDataBase() {
        for (CategoryBean categoryBean : categoryList) {
            dbHandler.addCategory(categoryBean);
        }

    }

    private void addProductsIntoDataBase() {
        for (ProductBean productBean : productsList)
            dbHandler.addProduct(productBean);

    }


    private void addNewCategoryInDb() {
        CategoryBean category = new CategoryBean();
        category.setCategoryParentId(0);
        category.setCategoryId(-123);
        category.setCategoryName("New Models");
        category.setCategoryPosition(0);
        category.setCategoryDesc("");
        category.setImageLocalPath("");
        category.setImageId(0);
        if (categoryList.size() > 0) {
            category.setImageServerPath(categoryList.get(0).getImageServerPath());
            category.setCategoryImage(categoryList.get(0).getCategoryImage());
        } else {
            category.setImageServerPath("");
            category.setCategoryImage("");
        }
        dbHandler.addCategory(category);
    }

    /*@Override
    public boolean onCreateOptionsMenu(Menu menu) {
//        MenuInflater inflater = getMenuInflater();
//        inflater.inflate(R.menu.menu_home, menu);
        new MenuInflater(getApplication()).inflate(R.menu.menu_home, menu);

        return true;
    }


    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.update) {
            checkUpdateFromServer();
        }

        return super.onOptionsItemSelected(item);
    }*/
    @Override
    protected void onDestroy() {
        super.onDestroy();
        unRegisterReceiver.unregister();
    }


    public interface UnRegisterReceiver {
        void unregister();
    }
}
