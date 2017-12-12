package com.gionee.gioneeabc.adapters;

import android.content.Context;
import android.content.Intent;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.TutorialProductDetailActivity;
import com.gionee.gioneeabc.activities.TutorialProductsActivity;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.helpers.Util;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.display.RoundedBitmapDisplayer;

import java.util.List;

/**
 * Created by root on 4/10/16.
 */
public class TutorialProductListAdapter extends RecyclerView.Adapter<TutorialProductListAdapter.CustomViewHolder> {
    private Context context;
    private List<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> productList;
    private ImageLoader imageLoader;
    private DisplayImageOptions options;

    public TutorialProductListAdapter(Context context, final List<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> productList) {
        this.context = context;
        this.productList = productList;
        imageLoader = ImageLoader.getInstance();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(context)
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
                .displayer(new RoundedBitmapDisplayer(90))
                .build();
    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.tutorial_list_element, parent, false); //Inflating the layout
        CustomViewHolder vhItem = new CustomViewHolder(v); //Creating ViewHolder and passing the object of type view
        return vhItem;
    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, int position) {
        holder.tvElement.setText(productList.get(position).getProduct_name());
        if (productList.get(position).getIs_new()!=null && productList.get(position).getIs_new().equals("1")) {
            holder.tvNewProduct.setVisibility(View.VISIBLE);
        } else {
            holder.tvNewProduct.setVisibility(View.GONE);
        }
        try {
            if (productList.get(position).getPro_image().size() > 0) {
                ImageBean thumbnailImage = new ImageBean(productList.get(position).getPro_image().get(0).getImage_id(), productList.get(position).getPro_image().get(0).getName(), productList.get(position).getPro_image().get(0).getTitle(), productList.get(position).getPro_image().get(0).getPath());
                if (thumbnailImage != null) {
                    String imageUrl = "file:///" + thumbnailImage.getImageLocalPath();
                    imageLoader.displayImage(imageUrl, holder.ivIcon, options, null);
                }
            }else {
                holder.ivIcon.setImageResource(R.drawable.app_icon);
            }
        }catch (Exception e){}
    }

    @Override
    public int getItemCount() {
        return productList.size();
    }

    public class CustomViewHolder extends RecyclerView.ViewHolder {
        private TextView tvElement, tvNewProduct;
        private ImageView ivIcon;

        public CustomViewHolder(View itemView) {
            super(itemView);
            tvElement = (TextView) itemView.findViewById(R.id.tv_element);
            tvElement.setTypeface(Util.getRoboMedium(context));
            ivIcon = (ImageView) itemView.findViewById(R.id.iv_icon);
            tvNewProduct = (TextView) itemView.findViewById(R.id.tvNewProduct);
            itemView.setClickable(true);

            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    displayView(getPosition());
                }
            });
        }
    }

    public void displayView(int position) {
        String title = "";
        Intent i = new Intent(context, TutorialProductDetailActivity.class);
        i.putExtra("product", position);
        ((TutorialProductsActivity) context).startActivityForResult(i, 101);
    }
}
