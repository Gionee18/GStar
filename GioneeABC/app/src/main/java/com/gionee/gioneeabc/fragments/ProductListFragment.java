package com.gionee.gioneeabc.fragments;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.widget.NestedScrollView;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ProductsActivity;
import com.gionee.gioneeabc.adapters.ProductListAdapter;
import com.gionee.gioneeabc.bean.CategoryBean;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;

import java.util.List;

/**
 * Created by Linchpin25 on 1/29/2016.
 */
public class ProductListFragment extends Fragment {
    View root;
    RecyclerView recyclerView;
    NestedScrollView nestedScrollView;
    CategoryBean category;
    DataBaseHandler dbHandler;
    List<ProductBean> productsList;
    private final int GET_PRODUCTS = 101;
    NetworkTask networkTask;
    TextView tvNoProducts;
    RecyclerView.Adapter adapter;
    private boolean isViewShown = false;
    boolean cleanProductList = true, isFirst = false;


    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        root = inflater.inflate(R.layout.product_list_fragment, null);
        initView();

       /* if (!isViewShown) {

            fetchData();

        }*/

//        fetchData();
        return root;
    }

    /* @Override
     public void setUserVisibleHint(boolean isVisibleToUser) {

         if (isVisibleToUser) {
             if (getView() != null) {
                 isViewShown = true;

                 fetchData();
             } else {
                 isViewShown = false;
             }


         }

      //   super.setUserVisibleHint(isVisibleToUser);
     }
 */
    public void fetchData() {


        int num = ((ProductsActivity) getActivity()).selectedPage;

        //  int num = 1;
        category = ((ProductsActivity) getActivity()).categoryList.get(num);

        dbHandler = DataBaseHandler.getInstance(getActivity());
        if (category.getCategoryName().equals("New Models")) {
            productsList = dbHandler.getNewProducts();
        } else
            productsList = dbHandler.getProductsByCategory(category.getCategoryId());


        if (productsList == null) {
            //if (Util.isNetworkAvailable(getActivity()))
            //   getDataFromServer();

            recyclerView.setVisibility(View.GONE);
            tvNoProducts.setVisibility(View.VISIBLE);
            nestedScrollView.setVisibility(View.VISIBLE);

        } else {


            recyclerView.setVisibility(View.VISIBLE);
            tvNoProducts.setVisibility(View.GONE);
            nestedScrollView.setVisibility(View.GONE);


            if (productsList != null && productsList.size() > 0)
                adapter = new ProductListAdapter(getActivity(), productsList);
            recyclerView.setAdapter(adapter);

        }

    }


    private void initView() {

        nestedScrollView = (NestedScrollView) root.findViewById(R.id.nestedScrollView);


        recyclerView = (RecyclerView) root.findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        recyclerView.setVisibility(View.GONE);


        tvNoProducts = (TextView) root.findViewById(R.id.tvNoProducts);
        tvNoProducts.setTypeface(Util.getRoboMedium(getActivity()));
        //tvNoProducts.setVisibility(View.GONE);

    }



    private void setAdapter() {
        if (productsList != null) {
            adapter = new ProductListAdapter(getActivity(), productsList);
            recyclerView.setAdapter(adapter);
            addDataIntoDatabase();
        }
    }

    private void addDataIntoDatabase() {
        for (int i = 0; i < productsList.size(); i++) {
            dbHandler.addProduct(productsList.get(i));
            dbHandler.addImage(new ImageBean(productsList.get(i).getId(), productsList.get(i).getProductImage(), "PRODUCT", "", productsList.get(i).getProductImage(), productsList.get(i).getId()));

        }
    }




    @Override
    public void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);
        //   setUserVisibleHint(true);
    }

    @Override
    public void onResume() {
        super.onResume();
       /* boolean clean = ((ProductsActivity) getActivity()).cleanProductList;
        if (productsList != null && clean) {
            productsList.clear();
            adapter.notifyDataSetChanged();
            ((ProductsActivity) getActivity()).cleanProductList = true;

        }*/


    }


}
