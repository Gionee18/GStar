package com.gionee.gioneeabc.fragments;


import android.app.Dialog;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Point;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Display;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.RecomenderActivity;
import com.gionee.gioneeabc.adapters.RecommProductListAdapter;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.bean.RecommProductListBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.google.gson.Gson;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.List;

/**
 * A simple {@link Fragment} subclass.
 */
public class RecommProductListFragment extends Fragment implements View.OnClickListener, NetworkTask.Result {

    private static final int GET_FILTER_RESULT = 110;
    private View view;
    private TextView tvFilter, tvFilterAttrib;
    private LinearLayout llSort;
    private ImageView ivSearch;
    private RecyclerView recyclerView;
    private RelativeLayout rlFilterManu, rlFilterAttrib, rlSearch;
    private EditText etSearch;
    private TextView tvClear;
    private TextView tvNoResult;
    private RecommProductListAdapter adapter;
    private boolean isManufacturer = true;
    private List<RecommProductListBean.Datum> productList;

    private static final String NEWEST_FIRST = "newestFirst";
    private static final String HIGH_TO_LOW = "highToLow";
    private static final String LOW_TO_HIGH = "lowToHigh";
    private String selected = NEWEST_FIRST;
    NetworkTask networkTask;

    public RecommProductListFragment() {
        // Required empty public constructor
    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_recomm_product_list, container, false);
        initUI();
        Bundle bundle = getArguments();
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
        if (isManufacturer) {
            rlFilterManu.setVisibility(View.VISIBLE);
            rlFilterAttrib.setVisibility(View.GONE);
        } else {
            rlFilterManu.setVisibility(View.GONE);
            rlFilterAttrib.setVisibility(View.VISIBLE);
        }
        initListener();
        setData();
        return view;
    }

    private void initUI() {
        tvFilter = (TextView) view.findViewById(R.id.tv_filter);
        tvFilterAttrib = (TextView) view.findViewById(R.id.tv_filter_attrib);
        llSort = (LinearLayout) view.findViewById(R.id.ll_sort);
        ivSearch = (ImageView) view.findViewById(R.id.iv_search);
        rlFilterManu = (RelativeLayout) view.findViewById(R.id.rl_filter_manufacturer);
        rlFilterAttrib = (RelativeLayout) view.findViewById(R.id.rl_filter_attrib);
        rlSearch = (RelativeLayout) view.findViewById(R.id.rl_search);
        tvClear = (TextView) view.findViewById(R.id.tv_clear);
        etSearch = (EditText) view.findViewById(R.id.et_search);
        tvNoResult = (TextView) view.findViewById(R.id.tv_no_result);
        recyclerView = (RecyclerView) view.findViewById(R.id.recyclerView);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
    }

    private List<RecommProductListBean.Datum> tempList;

    private void initListener() {
        tvFilter.setOnClickListener(this);
        tvFilterAttrib.setOnClickListener(this);
        llSort.setOnClickListener(this);
        ivSearch.setOnClickListener(this);
        tvClear.setOnClickListener(this);
        etSearch.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                if (productList != null && tempList != null && tempList.size() > 0) {
                    if (s.length() > 0) {
                        productList.clear();
                        for (int j = 0; j < tempList.size(); j++) {
                            if (tempList.get(j).getProductName().toLowerCase().contains(s.toString().trim().toLowerCase())) {
                                productList.add(tempList.get(j));
                            }
                        }
                    } else {
                        productList.clear();
                        productList.addAll(tempList);
                    }
                    if (productList != null && productList.size() > 0) {
                        sortProductList();
                        adapter = new RecommProductListAdapter(getActivity(), productList);
                        recyclerView.setAdapter(adapter);
                        tvNoResult.setVisibility(View.GONE);
                    } else {
                        tvNoResult.setVisibility(View.VISIBLE);
                    }
                }
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });
    }

    private void setData() {
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(getActivity(), DataStore.AUTH_TOKEN)));
        if (isManufacturer) {
            int brandId = -1;
            StringBuilder selModelStringBuilder = new StringBuilder();
            Cursor cursor = DataBaseHandler.getInstance(getActivity()).getAllRecommModelData();
            if (cursor.moveToFirst()) {
                String responseModel = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
                if (!responseModel.isEmpty()) {
                    RecommNonGioneeModelBean recommNonGioneeModelBean1 = new Gson().fromJson(responseModel, RecommNonGioneeModelBean.class);
                    List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean1.getData();
                    if (brandNameList.size() > 0) {
                        for (int i = 0; i < brandNameList.size(); i++) {
                            if (brandNameList.get(i).isSelected()) {
                                brandId = brandNameList.get(i).getId();
                                List<RecommNonGioneeModelBean.Model> modelList = brandNameList.get(i).getModel();
                                if (modelList.size() > 0) {
                                    for (int j = 0; j < modelList.size(); j++) {
                                        if (modelList.get(j).isSelected()) {
                                            selModelStringBuilder.append(modelList.get(j).getId() + ",");
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }
            String modelIds = null;
            if (!selModelStringBuilder.toString().isEmpty()) {
                if (selModelStringBuilder.toString().charAt(selModelStringBuilder.toString().length() - 1) == ',') {
                    modelIds = selModelStringBuilder.toString().substring(0, selModelStringBuilder.toString().length() - 1);
                } else {
                    modelIds = selModelStringBuilder.toString();
                }
            }
            if (brandId != -1)
                params.add(new BasicNameValuePair("brand_id", "" + brandId));
            if (modelIds != null)
                params.add(new BasicNameValuePair("model_ids", modelIds));
        }

        List<RecommAttribBean.RecommAttribData> selAttribDataList = new ArrayList<>();
        Cursor cursor1 = DataBaseHandler.getInstance(getActivity()).getAllRecommAttribData();
        if (cursor1.moveToFirst()) {
            String attribResponse = cursor1.getString(cursor1.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            RecommAttribBean recommAttribBean = new Gson().fromJson(attribResponse, RecommAttribBean.class);
            List<RecommAttribBean.RecommAttribData> recommAttribDataList = recommAttribBean.getData();
            if (recommAttribDataList.size() > 0) {
                for (int i = 0; i < recommAttribDataList.size(); i++) {
                    final RecommAttribBean.RecommAttribData recommAttribData = recommAttribDataList.get(i);
                    List<String> selFilterListAttrib = recommAttribData.getSelSearchAttrib();
                    if (selFilterListAttrib != null && selFilterListAttrib.size() > 0) {
                        selAttribDataList.add(recommAttribData);
                    }
                }
            }
        }

        if (selAttribDataList.size() > 0) {
            for (int i = 0; i < selAttribDataList.size(); i++) {
                RecommAttribBean.RecommAttribData recommAttribData = selAttribDataList.get(i);
                params.add(new BasicNameValuePair(recommAttribData.getId_key(), "" + recommAttribData.getId()));
                List<String> selFilterList = recommAttribData.getSelSearchAttrib();
                StringBuilder stringBuilder = new StringBuilder();
                for (String s : selFilterList) {
                    stringBuilder.append(s + ",");
                }
                String searchAttribs = stringBuilder.toString().substring(0, stringBuilder.toString().length() - 1);
                params.add(new BasicNameValuePair(recommAttribData.getValue_key(), searchAttribs));
            }
        }
        networkTask = new NetworkTask(getActivity(), GET_FILTER_RESULT, params, null);
        networkTask.exposePostExecute(this);
        networkTask.execute(NetworkConstants.GET_RECOMM_FILTER_PRODUCT_LIST);
    }

    int searchCount = 0;

    @Override
    public void onClick(View v) {
        if (v == tvFilter || v == tvFilterAttrib) {
            openFilterActivity();
        } else if (v == llSort) {
            openSortWindow();
        } else if (v == ivSearch) {
//            if (searchCount==0) {
            rlSearch.setVisibility(View.VISIBLE);
//                searchCount++;
//            }else {
//                if (etSearch.getText().toString().isEmpty()){
//                    rlSearch.setVisibility(View.GONE);
//                    searchCount=0;
//                }
//            }
        } else if (v == tvClear) {
            etSearch.setText("");
        }
    }

    private void openSortWindow() {
        /*final ListPopupWindow listPopupWindow;
        String[] products = {"Newest First", "Price -- Low to High", "Price -- High to Low"};

        listPopupWindow = new ListPopupWindow(getActivity());
        listPopupWindow.setAdapter(new ArrayAdapter(getContext(), R.layout.row_popup, products));
        listPopupWindow.setAnchorView(llSort);
//        int width = Utils.getScreenWidth(getActivity());
//        listPopupWindow.setWidth(width * 3 / 4);
        listPopupWindow.setWidth(ListPopupWindow.WRAP_CONTENT);
        listPopupWindow.setHeight(ListPopupWindow.WRAP_CONTENT);

        listPopupWindow.setModal(true);
        listPopupWindow.setSelection(0);
        listPopupWindow.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                if (position == 0) {
                    listPopupWindow.dismiss();
                } else if (position == 1) {
                    listPopupWindow.dismiss();
                } else if (position == 2) {
                    listPopupWindow.dismiss();
                }
            }
        });
        listPopupWindow.show();*/
        if (productList != null && productList.size() > 0) {
//            final Dialog dialog = new Dialog(new ContextThemeWrapper(getActivity(), R.style.DialogSlideAnim));
            final Dialog dialog = new Dialog(getActivity());
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Display display = getActivity().getWindowManager().getDefaultDisplay();
            Point size = new Point();
            display.getSize(size);
            int width = size.x;
            dialog.setContentView(R.layout.dialog_recomm_sort);
            WindowManager.LayoutParams lp = new WindowManager.LayoutParams();
            dialog.getWindow().setGravity(Gravity.BOTTOM);
            dialog.getWindow().setBackgroundDrawableResource(android.R.color.transparent);
            lp.copyFrom(dialog.getWindow().getAttributes());
            lp.width = width;
            dialog.getWindow().setAttributes(lp);
            RelativeLayout rlNewestFirst = (RelativeLayout) dialog.findViewById(R.id.rl_newest_first);
            RelativeLayout rlLowToHigh = (RelativeLayout) dialog.findViewById(R.id.rl_low_to_high);
            RelativeLayout rlHighToLow = (RelativeLayout) dialog.findViewById(R.id.rl_high_to_low);
            final ImageView ivNewestFirst = (ImageView) dialog.findViewById(R.id.iv_newest_first);
            final ImageView ivLowToHigh = (ImageView) dialog.findViewById(R.id.iv_low_to_high);
            final ImageView ivHighToLow = (ImageView) dialog.findViewById(R.id.iv_high_to_low);

            setImageRadio(ivHighToLow, ivLowToHigh, ivNewestFirst);

            rlNewestFirst.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    selected = NEWEST_FIRST;
                    sortProductList();
                    setImageRadio(ivHighToLow, ivLowToHigh, ivNewestFirst);
                    if (adapter != null)
                        adapter.notifyDataSetChanged();
                    dialog.dismiss();
                }
            });
            rlLowToHigh.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    selected = LOW_TO_HIGH;
                    sortProductList();
                    setImageRadio(ivHighToLow, ivLowToHigh, ivNewestFirst);
                    if (adapter != null)
                        adapter.notifyDataSetChanged();
                    dialog.dismiss();
                }
            });
            rlHighToLow.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    selected = HIGH_TO_LOW;
                    sortProductList();
                    setImageRadio(ivHighToLow, ivLowToHigh, ivNewestFirst);
                    if (adapter != null)
                        adapter.notifyDataSetChanged();
                    dialog.dismiss();
                }
            });

            dialog.setCancelable(true);
            dialog.show();
        }
    }

    private void sortProductList() {
        if (selected.equalsIgnoreCase(NEWEST_FIRST)) {

            Collections.sort(productList, Collections.reverseOrder(new Comparator<RecommProductListBean.Datum>() {
                @Override
                public int compare(RecommProductListBean.Datum datum, RecommProductListBean.Datum t1) {
                    SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
                    Date date1 = null;
                    Date date2 = null;
                    try {
                        date1 = dateFormat.parse(datum.getLaunch_date());
                        date2 = dateFormat.parse(t1.getLaunch_date());

                        return date1.compareTo(date2);

                    } catch (Exception e) {
                        return 0;
                    }
                }
            }));

//            Collections.sort(productList, Collections.reverseOrder(new Comparator<RecommProductListBean.Datum>() {
//                public int compare(RecommProductListBean.Datum o1, RecommProductListBean.Datum o2) {
//                    return o1.getNewProductFlag().compareTo(o2.getNewProductFlag());
//                }
//            }));
        } else if (selected.equalsIgnoreCase(LOW_TO_HIGH)) {
            Collections.sort(productList, new Comparator<RecommProductListBean.Datum>() {
                public int compare(RecommProductListBean.Datum o1, RecommProductListBean.Datum o2) {
                    return sortByPrice(o1, o2);
                }
            });
        } else if (selected.equalsIgnoreCase(HIGH_TO_LOW)) {
            Collections.sort(productList, Collections.reverseOrder(new Comparator<RecommProductListBean.Datum>() {
                public int compare(RecommProductListBean.Datum o1, RecommProductListBean.Datum o2) {
                    return sortByPrice(o1, o2);
                }
            }));
        }
    }

    private int sortByPrice(RecommProductListBean.Datum o1, RecommProductListBean.Datum o2) {
        Integer o1Price;
        Integer o2Price;
        if (o1.getPrice() != null)
            o1Price = Integer.parseInt(o1.getPrice().replaceAll("[\\D]", ""));
        else
            o1Price = 0;
        if (o2.getPrice() != null)
            o2Price = Integer.parseInt(o2.getPrice().replaceAll("[\\D]", ""));
        else
            o2Price = 0;
        return o1Price.compareTo(o2Price);
    }

    private void setImageRadio(ImageView ivHighToLow, ImageView ivLowToHigh, ImageView ivNewestFirst) {
        if (selected.equalsIgnoreCase(NEWEST_FIRST)) {
            ivNewestFirst.setImageResource(R.drawable.ic_radio_fill);
            ivHighToLow.setImageResource(R.drawable.ic_radio_outline);
            ivLowToHigh.setImageResource(R.drawable.ic_radio_outline);
        } else if (selected.equalsIgnoreCase(LOW_TO_HIGH)) {
            ivNewestFirst.setImageResource(R.drawable.ic_radio_outline);
            ivHighToLow.setImageResource(R.drawable.ic_radio_outline);
            ivLowToHigh.setImageResource(R.drawable.ic_radio_fill);
        } else if (selected.equalsIgnoreCase(HIGH_TO_LOW)) {
            ivNewestFirst.setImageResource(R.drawable.ic_radio_outline);
            ivHighToLow.setImageResource(R.drawable.ic_radio_fill);
            ivLowToHigh.setImageResource(R.drawable.ic_radio_outline);
        }
    }

    private void openFilterActivity() {
        UIUtils.isFilterFromProduct = true;
        Intent intent = new Intent(getActivity(), RecomenderActivity.class);
        if (isManufacturer)
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
        else
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_ATTRIB);
        intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_MAIN);
        startActivity(intent);
        getActivity().finish();
    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {

        if (object != null && !object.isEmpty()) {
            RecommProductListBean recommProductListBean = new Gson().fromJson(object, RecommProductListBean.class);
            if (recommProductListBean.getStatus().equalsIgnoreCase("success")) {
                productList = recommProductListBean.getData();
                if (productList != null && productList.size() > 0) {
                    selected = NEWEST_FIRST;
                    sortProductList();
                    adapter = new RecommProductListAdapter(getActivity(), productList);
                    recyclerView.setAdapter(adapter);
                    tvNoResult.setVisibility(View.GONE);
                    tempList = new ArrayList<>();
                    tempList.addAll(productList);
                } else {
                    tvNoResult.setVisibility(View.VISIBLE);
                }
            }
        }
    }
}
