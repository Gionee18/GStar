package com.gionee.gioneeabc.adapters;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Environment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ProductDetailActivity;
import com.gionee.gioneeabc.activities.ProductsActivity;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.Util;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.display.RoundedBitmapDisplayer;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * Created by Linchpin25 on 1/29/2016.
 */
public class ProductListAdapter extends RecyclerView.Adapter<ProductListAdapter.CustomViewHolder> {

    Context context;
    List<ProductBean> productList;
    ImageLoader imageLoader;
    DisplayImageOptions options;
    long enqueue;
    BroadcastReceiver receiver;
    DownloadManager downloadmanager;
    ImageBean selectedImage;
    DataBaseHandler dbHandler;
    String url;
    HashMap<Long, Integer> hm;
    Animation animZoomIn;
    ArrayList<String> f = new ArrayList<String>();// list of file paths
    File[] listFile;

    public ProductListAdapter(Context context, final List<ProductBean> productList) {
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
        downloadmanager = (DownloadManager) context.getSystemService(Context.DOWNLOAD_SERVICE);
        dbHandler = DataBaseHandler.getInstance(context);
        hm = new HashMap<Long, Integer>();
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
                    long downloadId = intent.getLongExtra(
                            DownloadManager.EXTRA_DOWNLOAD_ID, 0);
                    DownloadManager.Query query = new DownloadManager.Query();
                    query.setFilterById(enqueue);
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c
                                .getInt(columnIndex)) {
                            //  Util.createToast(context, "Download completed... Please check your data in Requester Document folder");
                            // selectedImage.setImageLocalPath(Environment.getExternalStorageDirectory() + "/GioneeStar/" + selectedImage.getImageName());
                            int pos = hm.get(downloadId);
                            ProductBean productBean = productList.get(pos);


                            ImageBean image = new ImageBean(productBean.getImageId(), productBean.getProductImage(), "THUMBNAIL", Environment.getExternalStorageDirectory() + "/GioneeStar/" + productBean.getProductImage(), productBean.getProductImageServerPath(), productBean.getId());

                            dbHandler.addImage(image);
                        }
                        //else
                        // Util.createToast(context, "Download fail, please try again");
                    }
                }
            }
        };


    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.product_list_element, parent, false); //Inflating the layout
        CustomViewHolder vhItem = new CustomViewHolder(v); //Creating ViewHolder and passing the object of type view
        return vhItem;
    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, int position) {
        //selectedImage = new ImageBean(productList.get(position).getImageId(), productList.get(position).getProductImage(), "PRODUCT", "", productList.get(position).getProductImageServerPath(), productList.get(position).getId());
        holder.tvElement.setText(productList.get(position).getProductName());
        if (productList.get(position).getIsNewProduct().equals("1")) {
            holder.tvNewProduct.setVisibility(View.VISIBLE);
          /*  AnimationSet s = new AnimationSet(false);
            animZoomIn = AnimationUtils.loadAnimation(context,
                    R.anim.new_product_anim);
            s.addAnimation(animZoomIn);
            holder.tvNewProduct.setAnimation(s);
            animZoomIn.setRepeatCount(Animation.INFINITE);*/
        } else {
            holder.tvNewProduct.setVisibility(View.GONE);
        }
        ImageBean thumbnailImage = dbHandler.getProductThumbnailImage(productList.get(position).getId());
        if (thumbnailImage == null) {
            imageLoader.displayImage(NetworkConstants.BASE_URL + productList.get(position).getProductImageServerPath() + "/" + productList.get(position).getProductImage(), holder.ivIcon, options, null);
            fileDownload(position);


        } else {
           /* if (productList.get(position).getProductImageLocalPath() != null && !productList.get(position).getProductImageLocalPath().equals("")) {
                String imageUrl = "file:///" + productList.get(position).getProductImageLocalPath();
                imageLoader.displayImage(imageUrl, holder.ivIcon, options, null);
            }*/
            String imageUrl = "file:///" + thumbnailImage.getImageLocalPath();
            imageLoader.displayImage(imageUrl, holder.ivIcon, options, null);

        }

    }

    @Override
    public int getItemCount() {
        return productList.size();
    }

    public class CustomViewHolder extends RecyclerView.ViewHolder {

        TextView tvElement, tvNewProduct;
        ImageView ivIcon;


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
        Intent i = new Intent(context, ProductDetailActivity.class);
        i.putExtra("product", productList.get(position));
        ((ProductsActivity) context).startActivityForResult(i, 101);


    }

    public void fileDownload(int i) {
        ProductBean product = productList.get(i);

        if(!Util.checkImageIAlreadyExist(product.getProductImage())) {
            File direct = new File(Environment.getExternalStorageDirectory()
                    + "/GioneeStar");

            if (!direct.exists()) {
                direct.mkdirs();
            }
            try {
                if (!checkImageIAlreadyExist(product.getProductImage())) {
                    url = NetworkConstants.BASE_URL + product.getProductImageServerPath() + "/" + product.getProductImage();
                    Uri downloadUri = Uri.parse(url);
                    DownloadManager.Request request = new DownloadManager.Request(
                            downloadUri);

                    request.setAllowedNetworkTypes(
                            DownloadManager.Request.NETWORK_WIFI
                                    | DownloadManager.Request.NETWORK_MOBILE)
                            .setAllowedOverRoaming(false)
                            .setTitle("GioneeStar")
                            .setNotificationVisibility(DownloadManager.Request.VISIBILITY_HIDDEN)
                            .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery+"GioneeStar", product.getProductImage());

                    enqueue = downloadmanager.enqueue(request);
                    hm.put(enqueue, i);
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

    }


    public boolean checkImageIAlreadyExist(String imageName)
    {
        File file= new File(Environment.getExternalStorageDirectory(), NetworkConstants.hideFolderFromGallery+"GioneeStar");
        if (file.isDirectory()&& listFile==null)
        {
            listFile = file.listFiles();
            for (int i = 0; i < listFile.length; i++)
            {
                f.add(listFile[i].getName());
            }
        }
        if(f.contains(imageName))
            return true;
        else
            return false;
    }

}
