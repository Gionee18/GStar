package com.gionee.gioneeabc.fragments;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.support.annotation.Nullable;
import android.support.v4.view.ViewPager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.MainActivity;
import com.gionee.gioneeabc.activities.ProductsActivity;
import com.gionee.gioneeabc.activities.RecomenderActivity;
import com.gionee.gioneeabc.activities.TutorialProductsActivity;
import com.gionee.gioneeabc.activities.UpdateActivity;
import com.gionee.gioneeabc.adapters.ImagesAdapter;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.FixedSpeedScroller;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.NetworkTask.Result;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.lang.reflect.Field;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import me.relex.circleindicator.CircleIndicator;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class HomeFragment extends ParentFragment implements View.OnClickListener, Result {

    View rootView;
    TextView tvProducts, tvTraining, tvTutorials, tvEtc, tvRecommender, tvLearning, tvUpdates, tvForum;
    // int[] imagesList = {R.drawable.db1, R.drawable.db2, R.drawable.db3, R.drawable.db4};
    ViewPager viewPager;
    ImagesAdapter adapter = null;
    LinearLayout llProduct, llTraining, llTutorials, llEtc, llRecommender, llLearning, llUpdates, llForum;
    NetworkTask networkTask;
    CircleIndicator ipIndicator;
    private final int DASBOARD_IMAGES = 100;
    List<ImageBean> imageList;
    DownloadManager downloadmanager;
    BroadcastReceiver imagereceiver;
    int page = 0;
    long enqueue;
    String url = null;
    HashMap<Long, Integer> hm;
    DataBaseHandler dbHandler;
    private Timer timer;
    boolean isVisible = true, isCompleted = false;


    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        rootView = inflater.inflate(R.layout.home_fragment, null);
        initViews();
        GStarApplication.getInstance().trackScreenView("Home Screen");

        hm = new HashMap<Long, Integer>();
        dbHandler = DataBaseHandler.getInstance(getActivity());
        /*if (Util.isNetworkAvailable(getActivity())) {
            getDashboardImagesFromServer();
        } else {
            getDashboardImagesFromLocal();
        }*/
        if (dbHandler.getDashBoardImages() == null) {
            getDashboardImagesFromServer();
        } else {
            getDashboardImagesFromLocal();
        }

        downloadmanager = (DownloadManager) getActivity().getSystemService(Context.DOWNLOAD_SERVICE);

        imagereceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action) && isVisible) {
                    long downloadId = intent.getLongExtra(
                            DownloadManager.EXTRA_DOWNLOAD_ID, 0);
                    DownloadManager.Query query = new DownloadManager.Query();
                    query.setFilterById(downloadId);
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c
                                .getInt(columnIndex)) {
                            if (hm != null && hm.size() > 0) {
                                if (hm.containsKey(downloadId)) {
                                    int pos = hm.get(downloadId);
                                    ImageBean image = imageList.get(pos);
                                    dbHandler.addImageLocalPath(image.getImageId(), Environment.getExternalStorageDirectory() + "/GioneeStar/" + image.getImageName());
                                }
                            }

                        }
                        //else
                        // Util.createToast(context, "Download fail, please try again");
                    }
                }
            }
        };

        getActivity().registerReceiver(imagereceiver, new IntentFilter(
                DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        // getCategoriesFromServer();
        try {
            Field mScroller;
            mScroller = ViewPager.class.getDeclaredField("mScroller");
            mScroller.setAccessible(true);
            FixedSpeedScroller scroller = new FixedSpeedScroller(viewPager.getContext());
            mScroller.set(viewPager, scroller);
        } catch (NoSuchFieldException e) {
        } catch (IllegalArgumentException e) {
        } catch (IllegalAccessException e) {
        }
        pageSwitcher();

        return rootView;
    }


    private void initViews() {
        llProduct = (LinearLayout) rootView.findViewById(R.id.ll_product);
        llProduct.setOnClickListener(this);

        /*new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (llProduct.getHeight() > llProduct.getWidth()) {
                    LinearLayout.LayoutParams layoutParams = new LinearLayout.LayoutParams(llProduct.getWidth(), llProduct.getWidth());
                    llProduct.setLayoutParams(layoutParams);
                } else if (llProduct.getHeight() < llProduct.getWidth()) {
                    LinearLayout.LayoutParams layoutParams = new LinearLayout.LayoutParams(llProduct.getHeight(), llProduct.getHeight());
                    llProduct.setLayoutParams(layoutParams);
                }
            }
        }, 50);*/

        llRecommender = (LinearLayout) rootView.findViewById(R.id.ll_recommender);
        llRecommender.setOnClickListener(this);

        llTutorials = (LinearLayout) rootView.findViewById(R.id.ll_tutorials);
        llTutorials.setOnClickListener(this);

        llUpdates = (LinearLayout) rootView.findViewById(R.id.ll_updates);
        llUpdates.setOnClickListener(this);
        tvUpdates = (TextView) rootView.findViewById(R.id.tv_updates);
        tvUpdates.setTypeface(Util.getRoboMedium(getActivity()));
//        llLearning = (LinearLayout) rootView.findViewById(R.id.ll_learning);
//        llLearning.setOnClickListener(this);
//        tvLearning = (TextView) rootView.findViewById(R.id.tv_learning);
//        tvLearning.setTypeface(Util.getRoboMedium(getActivity()));
//        llForum = (LinearLayout) rootView.findViewById(R.id.ll_forum);
//        llForum.setOnClickListener(this);
//        tvForum = (TextView) rootView.findViewById(R.id.tv_forum);
//        tvForum.setTypeface(Util.getRoboMedium(getActivity()));

        tvProducts = (TextView) rootView.findViewById(R.id.tvProduct);
        tvProducts.setTypeface(Util.getRoboMedium(getActivity()));

        tvRecommender = (TextView) rootView.findViewById(R.id.tv_recommender);
        tvRecommender.setTypeface(Util.getRoboMedium(getActivity()));


        tvTutorials = (TextView) rootView.findViewById(R.id.tvTutorials);
        tvTutorials.setTypeface(Util.getRoboMedium(getActivity()));


        viewPager = (ViewPager) rootView.findViewById(R.id.viewPager);
        ipIndicator = (CircleIndicator) rootView.findViewById(R.id.indicator);
    }

    private void settingAdapter(boolean isImageAlreadySaved) {
        adapter = new ImagesAdapter(getActivity(), imageList, true, isImageAlreadySaved, this);
        viewPager.setAdapter(adapter);
        try {
            ipIndicator.setViewPager(viewPager);
        } catch (Exception e) {
        }
        viewPager.setCurrentItem(0);

        if (!isImageAlreadySaved)
            addingImagesIntoDataBase();


    }


    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.ll_product:
                startActivity(new Intent(getActivity(), ProductsActivity.class));
                break;
            case R.id.ll_recommender:
//                Util.performFragmentTransaction(getActivity(), R.id.container, UIUtils.TAG_RECOMMENDER_FRAGMENT, true, null, new RecomenderFragment());
//                ((MainActivity) getActivity()).animateToBackArrow();
                UIUtils.isFilterFromProduct = false;
                clearFilters();
                Intent intent = new Intent(getActivity(), RecomenderActivity.class);
                intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
//                if (Util.isBrandModelSelected(getActivity()))
//                    intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_FILTER);
//                else
                intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_MAIN);
                startActivity(intent);
//                Util.createSnackBar(tvProducts, "work under progress");
                break;
            case R.id.ll_tutorials:
                startActivity(new Intent(getActivity(), TutorialProductsActivity.class));
                /*Util.createSnackBar(tvProducts, "work under progress");*/
                break;
            case R.id.ll_updates:
                startActivity(new Intent(getActivity(), UpdateActivity.class));
                // Util.createSnackBar(tvProducts, "work under progress");
                break;
//            case R.id.ll_forum:
//                //startActivity(new Intent(getActivity(),TutorialProductsActivity.class));
//                Util.createSnackBar(tvProducts, "work under progress");
//                break;
//            case R.id.ll_learning:
//                Util.createSnackBar(tvProducts, "work under progress");
//                // startActivity(new Intent(getActivity(),UpdateDetailActivity.class));
////                startActivity(new Intent(getActivity(), CompareSpecifictionActivity.class));
//                break;


        }

    }

    private void addingImagesIntoDataBase() {
        for (int i = 0; i < imageList.size(); i++) {
            ImageBean image = imageList.get(i);
            dbHandler.addImage(image);
          /*  image.setImageByte(Util.getLogoImage(image.getImageServerPath()));
            dbHandler.addImageInCollection(image);*/
            fileDownload(i);
        }
    }

    public void getDashboardImagesFromServer() {
        networkTask = new NetworkTask(getActivity(), DASBOARD_IMAGES);
        networkTask.exposePostExecute(HomeFragment.this);
        networkTask.execute(NetworkConstants.GET_DASHBOARD_IMAGES_URL + DataStore.getAuthToken(getActivity(), DataStore.AUTH_TOKEN));

    }

    private void getDashboardImagesFromLocal() {
        imageList = dbHandler.getDashBoardImages();
        settingAdapter(true);
        /*if (imageList.size() == DataStore.getDashBoardImagesCount(getActivity(), DataStore.DASHBOARD_IMAGES_COUNT))
            settingAdapter(true);
        else {
            getDashboardImagesFromServer();
        }*/

    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("") && id == DASBOARD_IMAGES) {
            try {
                JSONObject main = new JSONObject(object);
                imageList = new ArrayList<ImageBean>();
                if (main.getString("status").equalsIgnoreCase("success")) {
                    DataStore.setDashBoardImagesCount(getActivity(), main.optInt("count"));

                    JSONArray mainArray = main.getJSONArray("data");
                    for (int i = 0; i < mainArray.length(); i++) {
                        JSONObject child = mainArray.getJSONObject(i);
                        ImageBean image = new ImageBean(child.optInt("id"), child.optString("value"), "DASHBOARD", "", child.optString("path"), 0);
                        imageList.add(image);
                    }
                    settingAdapter(false);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }

        }
    }


    public void fileDownload(int i) {

       /* if(!Util.checkImageIAlreadyExist(imageList.get(i).getImageName())) {*/
        ImageBean img = imageList.get(i);
        if (!Util.checkImageIAlreadyExist(img.getImageName())) {
            File direct = new File(Environment.getExternalStorageDirectory()
                    + NetworkConstants.hideFolderFromGallery + "GioneeStar");
            if (!direct.exists()) {
                direct.mkdirs();
            }
            try {
                url = NetworkConstants.BASE_URL + img.getImageServerPath() + "/" + img.getImageName();
                Uri downloadUri = Uri.parse(url);
                DownloadManager.Request request = new DownloadManager.Request(
                        downloadUri);

                request.setAllowedNetworkTypes(
                        DownloadManager.Request.NETWORK_WIFI
                                | DownloadManager.Request.NETWORK_MOBILE)
                        .setAllowedOverRoaming(false)
                        .setTitle("GioneeStar")
                        // .setNotificationVisibility(DownloadManager.Request.VISIBILITY_HIDDEN)
                        .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", img.getImageName());

                enqueue = downloadmanager.enqueue(request);
                hm.put(enqueue, i);

            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        // }
    }

    @Override
    public void onPause() {
        super.onPause();
        isVisible = false;
    }

    @Override


    public void onDestroy() {
        super.onDestroy();
        getActivity().unregisterReceiver(imagereceiver);
    }

    void pageSwitcher() {
        timer = new Timer();
        timer.scheduleAtFixedRate(new ReminderTask(), 5 * 1000, 5
                * 1000);
    }

    class ReminderTask extends TimerTask {
        @Override
        public void run() {
            try {
                if (getActivity() != null) {
                    getActivity().runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            if (imageList != null) {
                                if (page >= imageList.size()) { // In my case the number of pages are 5
                                    page = 0;
                                } else {
                                    viewPager.setCurrentItem(page++);
                                }
                            }
                        }
                    });
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        ((MainActivity) getActivity()).toolBar.setTitle(getString(R.string.app_name));
    }

    private void clearFilters() {
        Cursor cursor = DataBaseHandler.getInstance(getActivity()).getAllRecommModelData();
        if (cursor.moveToFirst()) {
            String modelResponse = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            RecommNonGioneeModelBean recommNonGioneeModelBean = new Gson().fromJson(modelResponse, RecommNonGioneeModelBean.class);
            ArrayList<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean.getData();
            if (brandNameList.size() > 0) {
                for (int i = 0; i < brandNameList.size(); i++) {
                    if (brandNameList.get(i).isSelected()) {
                        brandNameList.get(i).setIsSelected(false);
                        List<RecommNonGioneeModelBean.Model> modelList = brandNameList.get(i).getModel();
                        if (modelList.size() > 0) {
                            for (int j = 0; j < modelList.size(); j++) {
                                modelList.get(j).setIsSelected(false);
                            }
                        }
                        break;
                    }
                }
            }
            recommNonGioneeModelBean.setData(brandNameList);
            DataBaseHandler.getInstance(getActivity()).deleteAllRecommModelData();
            DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommNonGioneeModelBean), DataBaseHandler.TYPE_RECOMM_MODEL);
        }
        Cursor cursor1 = DataBaseHandler.getInstance(getActivity()).getAllRecommAttribData();
        if (cursor1.moveToFirst()) {
            String attribResponse = cursor1.getString(cursor1.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            RecommAttribBean recommAttribBean = new Gson().fromJson(attribResponse, RecommAttribBean.class);
            for (int i = 0; i < recommAttribBean.getData().size(); i++) {
                if (recommAttribBean.getData().get(i).getSelSearchAttrib() != null)
                    recommAttribBean.getData().get(i).getSelSearchAttrib().clear();
            }
            DataBaseHandler.getInstance(getActivity()).deleteAllRecommAttribData();
            DataBaseHandler.getInstance(getActivity()).addGetData(new Gson().toJson(recommAttribBean), DataBaseHandler.TYPE_RECOMM_ATTRIB);
        }
    }
}
