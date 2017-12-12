package com.gionee.gioneeabc.fragments;


import android.database.Cursor;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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
import com.gionee.gioneeabc.helpers.UIUtils;
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

/**
 * A simple {@link Fragment} subclass.
 */
public class CompareSpecificationFragment extends Fragment implements AdapterView.OnItemSelectedListener, NetworkTask.Result {

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

    private TextView tvGionneName, tvNonGionneName, tvDisclaimer;
    ImageView ivGionee, ivNonGionee;

    private ImageLoader imageLoader;
    private DisplayImageOptions options;

    private View fragment;


    public CompareSpecificationFragment() {
        // Required empty public constructor
    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment

        fragment = inflater.inflate(R.layout.fragment_compare_specification, container, false);
        Util.hideKeyBoard(getActivity());
        init();
        return fragment;
    }

    private void init() {

        imageLoader = ImageLoader.getInstance();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(getActivity())
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
        dbHandler = DataBaseHandler.getInstance(getActivity());

        tvDisclaimer = (TextView) fragment.findViewById(R.id.disclaimer);
        tvDisclaimer.setText(DataStore.getDisclaimer(getContext()));

        tvGionneName = (TextView) fragment.findViewById(R.id.tv_gionne_name);
        tvNonGionneName = (TextView) fragment.findViewById(R.id.tv_nongionne_name);
        ivGionee = (ImageView) fragment.findViewById(R.id.iv_gionne);
        ivNonGionee = (ImageView) fragment.findViewById(R.id.iv_nongionne);

        recyclerView = (RecyclerView) fragment.findViewById(R.id.recyclerView);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        adaptor = new CompareSpecficationAdaptor(subcategoryList, getActivity());
        recyclerView.setAdapter(adaptor);
        recyclerView.setNestedScrollingEnabled(false);


        tvGionee = (AutoCompleteTextView) fragment.findViewById(R.id.tv_gionne);
        tvNonGionee = (AutoCompleteTextView) fragment.findViewById(R.id.tv_non_gionne);

        boolean isNonGioneeModelFound = false;
        Cursor cursor = dbHandler.getAllRecommModelData();
        if (cursor.moveToFirst()) {
            String updateCatRes = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            recommNonGioneeModelBean = new Gson().fromJson(updateCatRes, RecommNonGioneeModelBean.class);
            if (recommNonGioneeModelBean != null) {
                for (int i = 0; i < recommNonGioneeModelBean.getData().size(); i++) {
                    for (int j = 0; j < recommNonGioneeModelBean.getData().get(i).getModel().size(); j++) {
                        String name = recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName();
                        nonGionneModelName.add(name);
                        if (recommNonGioneeModelBean.getData().get(i).isSelected() && recommNonGioneeModelBean.getData().get(i).getModel().get(j).isSelected() && isNonGioneeModelFound == false) {
                            isNonGioneeModelFound = true;
                            UIUtils.selectedNonGioneeModel = recommNonGioneeModelBean.getData().get(i).getModel().get(j).getId();
                            tvNonGionneName.setText(recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName());
                            tvNonGionee.setText(recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName());
                            tvNonGionee.dismissDropDown();
                            Util.hideKeyBoard(getActivity());
                            if (recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage() != null
                                    && recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0) != null
                                    && !recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getName().equals("")) {
                                String imageUrl = NetworkConstants.BASE_URL + recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getPath() + "/thumbnail_medium/" + recommNonGioneeModelBean.getData().get(i).getModel().get(j).getAssetImage().get(0).getName();
                                imageLoader.displayImage(imageUrl, ivNonGionee, options, null);
                            }
                        }

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


        ArrayAdapter<String> adapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_dropdown_item_1line, gionneModelName);
        tvGionee.setThreshold(1);
        tvGionee.setAdapter(adapter);
        tvGionee.setOnItemSelectedListener(this);
        tvGionee.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int postion, long l) {
                Util.hideKeyBoard(getActivity());
                String name = adapterView.getItemAtPosition(postion).toString();
                for (int i = 0; i < productBeen.size(); i++) {
                    if (productBeen.get(i).getProductName().equalsIgnoreCase(name)) {
                        UIUtils.selectedGioneeModel = productBeen.get(i).getId();
                        tvGionneName.setText(productBeen.get(i).getProductName());
                        if (productBeen.get(i).getProductName() != null && !productBeen.get(i).getProductName().equalsIgnoreCase("")) {
                            String imageUrl = NetworkConstants.BASE_URL + productBeen.get(i).getProductImageServerPath() + "/" + productBeen.get(i).getProductImage();
                            imageLoader.displayImage(imageUrl, ivGionee, options, null);
                        }
                        break;
                    }
                }
                if (UIUtils.selectedNonGioneeModel > 0 && UIUtils.selectedGioneeModel > 0)
                    getDataCompareDataFromServer(UIUtils.selectedGioneeModel, UIUtils.selectedNonGioneeModel);
            }
        });


        adapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_dropdown_item_1line, nonGionneModelName);
        tvNonGionee.setThreshold(1);
        tvNonGionee.setAdapter(adapter);
        tvNonGionee.setOnItemSelectedListener(this);
        tvNonGionee.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int poition, long l) {
                Util.hideKeyBoard(getActivity());
                String name = adapterView.getItemAtPosition(poition).toString();
                for (int i = 0; i < recommNonGioneeModelBean.getData().size(); i++) {
                    for (int j = 0; j < recommNonGioneeModelBean.getData().get(i).getModel().size(); j++) {
                        if (recommNonGioneeModelBean.getData().get(i).getModel().get(j).getModelName().equalsIgnoreCase(name)) {
                            UIUtils.selectedNonGioneeModel = recommNonGioneeModelBean.getData().get(i).getModel().get(j).getId();
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
                if (UIUtils.selectedNonGioneeModel > 0 && UIUtils.selectedGioneeModel > 0)
                    getDataCompareDataFromServer(UIUtils.selectedGioneeModel, UIUtils.selectedNonGioneeModel);
            }
        });


        Bundle bundle = this.getArguments();
        if (bundle != null) {
            int id = 0;
            if (!bundle.getString("gionee_id", "").equals(""))
                id = Integer.parseInt(bundle.getString("gionee_id", ""));
            for (int i = 0; i < productBeen.size(); i++) {
                if (productBeen.get(i).getId() == (id)) {
                    UIUtils.selectedGioneeModel = productBeen.get(i).getId();
                    tvGionneName.setText(productBeen.get(i).getProductName());
                    tvGionee.setText(productBeen.get(i).getProductName());
                    tvGionee.dismissDropDown();
                    Util.hideKeyBoard(getActivity());
                    if (productBeen.get(i).getProductName() != null && !productBeen.get(i).getProductName().equalsIgnoreCase("")) {
                        String imageUrl = NetworkConstants.BASE_URL + productBeen.get(i).getProductImageServerPath() + "/" + productBeen.get(i).getProductImage();
                        imageLoader.displayImage(imageUrl, ivGionee, options, null);
                    }
                    if (UIUtils.selectedNonGioneeModel > 0 && UIUtils.selectedGioneeModel > 0)
                        getDataCompareDataFromServer(UIUtils.selectedGioneeModel, UIUtils.selectedNonGioneeModel);
                    break;
                }
            }
        }

        if (UIUtils.compareSpecficationBean != null) {
            if (UIUtils.compareSpecficationBean.getData() != null && UIUtils.compareSpecficationBean.getData().size() > 0) {
                if ((UIUtils.selectedNonGioneeModel == -1 && UIUtils.selectedGioneeModel == -1) || (UIUtils.selectedGioneeModel == UIUtils.compareSpecficationBean.getSelectedGioneeModelId() && UIUtils.selectedNonGioneeModel == UIUtils.compareSpecficationBean.getSelectedNonGioneeModelId())) {
                    tvNonGionneName.setText(UIUtils.compareSpecficationBean.getSelectedNonGioneeModel());
                    tvNonGionee.setText(UIUtils.compareSpecficationBean.getSelectedNonGioneeModel());
                    tvNonGionee.dismissDropDown();
                    Util.hideKeyBoard(getActivity());
                    UIUtils.selectedNonGioneeModel = UIUtils.compareSpecficationBean.getSelectedNonGioneeModelId();

                    tvGionneName.setText(UIUtils.compareSpecficationBean.getSelectedGioneeModel());
                    tvGionee.setText(UIUtils.compareSpecficationBean.getSelectedGioneeModel());
                    tvGionee.dismissDropDown();
                    Util.hideKeyBoard(getActivity());
                    UIUtils.selectedGioneeModel = UIUtils.compareSpecficationBean.getSelectedGioneeModelId();


                    for (int i = 0; i < UIUtils.compareSpecficationBean.getData().size(); i++) {
                        String catName = UIUtils.compareSpecficationBean.getData().get(i).getCatName();
                        for (int j = 0; j < UIUtils.compareSpecficationBean.getData().get(i).getSubcategory().size(); j++) {
                            CompareSpecficationBean.CompareSubcategory compareSubcategory = UIUtils.compareSpecficationBean.getData().get(i).getSubcategory().get(j);
                            if (!catName.equalsIgnoreCase("")) {
                                compareSubcategory.setCatName(catName);
                                catName = "";
                            }
                            subcategoryList.add(compareSubcategory);
                        }
                    }
                    adaptor.notifyDataSetChanged();
                } else {
                    UIUtils.compareSpecficationBean = null;
                    subcategoryList.clear();
                    adaptor.notifyDataSetChanged();
                }
            }
        }


    }

    private void getDataCompareDataFromServer(int gionee_model, int other_model) {
        subcategoryList.clear();
        adaptor.notifyDataSetChanged();
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(getActivity(), DataStore.AUTH_TOKEN)));
        params.add(new BasicNameValuePair("gionee_model", gionee_model + ""));
        params.add(new BasicNameValuePair("other_model", other_model + ""));
        NetworkTask networkTask = new NetworkTask(getActivity(), GET_DATA, params, null);
        networkTask.exposePostExecute(this);
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
                            UIUtils.compareSpecficationBean = new Gson().fromJson(object, CompareSpecficationBean
                                    .class);
                            if (UIUtils.compareSpecficationBean.getData() != null && UIUtils.compareSpecficationBean.getData().size() > 0) {
                                UIUtils.compareSpecficationBean.setSelectedGioneeModel(tvGionneName.getText().toString());
                                UIUtils.compareSpecficationBean.setSelectedNonGioneeModel(tvNonGionneName.getText().toString());
                                UIUtils.compareSpecficationBean.setSelectedGioneeModelId(UIUtils.selectedGioneeModel);
                                UIUtils.compareSpecficationBean.setSelectedNonGioneeModelId(UIUtils.selectedNonGioneeModel);
                                for (int i = 0; i < UIUtils.compareSpecficationBean.getData().size(); i++) {
                                    String catName = UIUtils.compareSpecficationBean.getData().get(i).getCatName();
                                    for (int j = 0; j < UIUtils.compareSpecficationBean.getData().get(i).getSubcategory().size(); j++) {
                                        CompareSpecficationBean.CompareSubcategory compareSubcategory = UIUtils.compareSpecficationBean.getData().get(i).getSubcategory().get(j);
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

        Util.hideKeyBoard(getActivity());

    }


}
