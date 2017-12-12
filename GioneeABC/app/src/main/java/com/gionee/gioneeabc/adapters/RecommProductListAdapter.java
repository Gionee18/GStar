package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.content.Intent;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ProductDetailActivity;
import com.gionee.gioneeabc.activities.RecomenderActivity;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.RecommProductListBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.squareup.picasso.Picasso;

import java.util.List;

/**
 * Created by Linchpin
 */
public class RecommProductListAdapter extends RecyclerView.Adapter<RecommProductListAdapter.ViewHolder> {
    private Activity activity;
    private List<RecommProductListBean.Datum> productList;

    public RecommProductListAdapter(Activity activity, List<RecommProductListBean.Datum> productList) {
        this.activity = activity;
        this.productList = productList;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.row_recomm_product, parent, false); //Inflating the layout
        ViewHolder vhItem = new ViewHolder(v); //Creating ViewHolder and passing the object of type view
        return vhItem;
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        RecommProductListBean.Datum datum = productList.get(position);
        holder.tvProduct.setText(datum.getProductName());
        List<RecommProductListBean.ProAsset> proAssetList = datum.getProAsset();
        if (proAssetList != null && proAssetList.size() > 0) {
            RecommProductListBean.ProAsset proAsset = proAssetList.get(0);
            String path = NetworkConstants.BASE_URL + proAsset.getPath() + "/thumbnail/" + proAsset.getName();
            Picasso.with(activity).load(path).into(holder.ivProduct);
        } else {
            holder.ivProduct.setImageResource(R.drawable.app_icon);
        }
    }

    @Override
    public int getItemCount() {
        return productList.size();
    }


    public class ViewHolder extends RecyclerView.ViewHolder {
        private ImageView ivProduct;
        private TextView tvProduct, tvCompare;

        public ViewHolder(View itemView) {
            super(itemView);
            itemView.setClickable(true);
            ivProduct = (ImageView) itemView.findViewById(R.id.iv_product);
            tvProduct = (TextView) itemView.findViewById(R.id.tv_product);
            tvCompare = (TextView) itemView.findViewById(R.id.tv_compare);
            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    Intent i = new Intent(activity, ProductDetailActivity.class);
                    i.putExtra("product_from", "recomm");
                    ProductBean productBean = DataBaseHandler.getInstance(activity).getProductById(productList.get(getPosition()).getId());
                    if (productBean != null) {
                        i.putExtra("product", productBean);
                        activity.startActivity(i);
                    }
                }
            });
            tvCompare.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    UIUtils.compareSpecficationBean = null;
                    UIUtils.selectedGioneeModel = -1;
                    UIUtils.selectedNonGioneeModel = -1;
                    Intent intent = new Intent(activity, RecomenderActivity.class);
                    intent.putExtra("gionee_id", "" + productList.get(getPosition()).getId());
                    activity.startActivity(intent);
                    activity.finish();
                }
            });
        }
    }
}
