package com.gionee.gioneeabc.adapters;

import android.annotation.SuppressLint;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.graphics.Matrix;
import android.graphics.PointF;
import android.support.v4.view.PagerAdapter;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.AnimationSet;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.fragments.HomeFragment;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.squareup.picasso.Picasso;

import java.io.File;
import java.util.List;

/**
 * Created by Linchpin25 on 1/22/2016.
 */
public class ImagesAdapter extends PagerAdapter {

    Context con;
    Animation animZoomIn, animRight;
    String url = null;
    long enqueue;
    BroadcastReceiver receiver;
    Matrix matrix = new Matrix();
    Matrix savedMatrix = new Matrix();
    PointF startPoint = new PointF();
    PointF midPoint = new PointF();
    float oldDist = 1f;
    static final int NONE = 0;
    static final int DRAG = 1;
    static final int ZOOM = 2;
    int mode = NONE;

    boolean isAnimation = false;
    List<ImageBean> imageList;

    LayoutInflater mLayoutInflater;
    HomeFragment homeFragment;
    ImageBean selectedImage = null;
    boolean isImageAlreadySaved = false;

    public ImagesAdapter(Context con, List<ImageBean> imageList) {
        this.con = con;
        mLayoutInflater = (LayoutInflater) con.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        this.imageList = imageList;
    }

    public ImagesAdapter(Context con, final List<ImageBean> imageList, boolean isAnimation, boolean isImageAlreadySaved) {
        this.con = con;
        mLayoutInflater = (LayoutInflater) con.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        this.imageList = imageList;
        this.isAnimation = isAnimation;
        this.isImageAlreadySaved = isImageAlreadySaved;

    }

    public ImagesAdapter(Context con, final List<ImageBean> imageList, boolean isAnimation, boolean isImageAlreadySaved, HomeFragment homeFragment) {
        this.con = con;
        mLayoutInflater = (LayoutInflater) con.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        this.imageList = imageList;
        this.isAnimation = isAnimation;
        this.isImageAlreadySaved = isImageAlreadySaved;
        this.homeFragment = homeFragment;

    }

     @Override
    public int getCount() {
        return imageList.size();
    }

    @Override
    public CharSequence getPageTitle(int position) {
        return super.getPageTitle(position);
    }

    @Override
    public Object instantiateItem(ViewGroup container, final int position) {

        View itemView = mLayoutInflater.inflate(R.layout.image_row, container, false);

        final ImageView imageView = (ImageView) itemView.findViewById(R.id.image);
        imageView.setTag(position);
        selectedImage = imageList.get(position);
        if (isImageAlreadySaved) {
            /*Picasso.Builder builder = new Picasso.Builder(con);
            builder.listener(new Picasso.Listener() {
                @Override
                public void onImageLoadFailed(Picasso picasso, Uri uri, Exception exception) {
                    *//*if (homeFragment != null) {
                        File file = new File(selectedImage.getImageLocalPath());
                        if (file.exists()) {
                            file.delete();
                            homeFragment.fileDownload(position);
                        }


                    }*//*

                    Picasso.with(con).load(NetworkConstants.BASE_URL + "/" + selectedImage.getImageServerPath() + "/thumbnail/" + selectedImage.getImageName()).into(imageView);
                    exception.printStackTrace();
                }
            });
            builder.build().load(new File(selectedImage.getImageLocalPath())).into(imageView);*/
            File file=new File(selectedImage.getImageLocalPath());
            if (TextUtils.isEmpty(selectedImage.getImageLocalPath()) || !file.exists() || file.length()<=0){
                Picasso.with(con).load(NetworkConstants.BASE_URL + "/" + selectedImage.getImageServerPath() + "/thumbnail_medium/" + selectedImage.getImageName()).into(imageView);
            }else {
                Picasso.with(con).load(file).into(imageView, new com.squareup.picasso.Callback() {
                    @Override
                    public void onSuccess() {

                    }

                    @Override
                    public void onError() {
                        Picasso.with(con).load(NetworkConstants.BASE_URL + "/" + selectedImage.getImageServerPath() + "/thumbnail_medium/" + selectedImage.getImageName()).into(imageView);
                    }
                });
            }

            // Picasso.with(con).load(new File(selectedImage.getImageLocalPath())).into(imageView);
        } else {

            Picasso.with(con).load(NetworkConstants.BASE_URL + "/" + selectedImage.getImageServerPath() + "/thumbnail_medium/" + selectedImage.getImageName()).into(imageView);
        }
        if (false) {    //isAnimation
            // load the animation
            AnimationSet s = new AnimationSet(false);
            animZoomIn = AnimationUtils.loadAnimation(con,
                    R.anim.zoom_in);
            animRight = AnimationUtils.loadAnimation(con, R.anim.right_anim);
            s.addAnimation(animZoomIn);
            s.addAnimation(animRight);
            imageView.setAnimation(s);
            animZoomIn.setRepeatCount(Animation.INFINITE);
        }

        imageView.setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                ImageView view = (ImageView) v;
                switch (event.getAction() & MotionEvent.ACTION_MASK) {
                    case MotionEvent.ACTION_DOWN:

                        savedMatrix.set(matrix);
                        startPoint.set(event.getX(), event.getY());
                        mode = DRAG;
                        break;

                    case MotionEvent.ACTION_POINTER_DOWN:

                        oldDist = spacing(event);

                        if (oldDist > 10f) {
                            savedMatrix.set(matrix);
                            midPoint(midPoint, event);
                            mode = ZOOM;
                        }
                        break;

                    case MotionEvent.ACTION_UP:

                    case MotionEvent.ACTION_POINTER_UP:
                        mode = NONE;

                        break;

                    case MotionEvent.ACTION_MOVE:
                        if (mode == DRAG) {
                            matrix.set(savedMatrix);
                            matrix.postTranslate(event.getX() - startPoint.x,
                                    event.getY() - startPoint.y);
                        } else if (mode == ZOOM) {
                            float newDist = spacing(event);
                            if (newDist > 10f) {
                                matrix.set(savedMatrix);
                                float scale = newDist / oldDist;
                                matrix.postScale(scale, scale, midPoint.x, midPoint.y);
                            }
                        }
                        break;

                }
                view.setImageMatrix(matrix);


                return true;
            }
        });
        container.addView(itemView);
        return itemView;

    }

    @SuppressLint("FloatMath")
    private float spacing(MotionEvent event) {
        float x = event.getX(0) - event.getX(1);
        float y = event.getY(0) - event.getY(1);
        return (float) (Math.sqrt(x * x + y * y));
    }

    private void midPoint(PointF point, MotionEvent event) {
        float x = event.getX(0) + event.getX(1);
        float y = event.getY(0) + event.getY(1);
        point.set(x / 2, y / 2);
    }


    @Override
    public boolean isViewFromObject(View view, Object object) {
        return view == ((LinearLayout) object);
    }

    @Override
    public void destroyItem(ViewGroup container, int position, Object object) {
        container.removeView((LinearLayout) object);
    }

    @Override
    public int getItemPosition(Object object) {
        return POSITION_NONE;
    }
}
