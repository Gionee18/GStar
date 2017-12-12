package com.gionee.gioneeabc.adapters;

import android.app.Dialog;
import android.app.DownloadManager;
import android.content.ActivityNotFoundException;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Environment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ShowFileView;
import com.gionee.gioneeabc.activities.TutorialProductDetailActivity;
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.FontImageView;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.Util;
import com.squareup.picasso.Picasso;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;

/**
 * Created by root on 4/10/16.
 */
public class TutorialProductDetailsAdapter extends RecyclerView.Adapter<TutorialProductDetailsAdapter.DocumentViewHolder> {
    //    ArrayList<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo> documentsList;
    Context mContext;
    HashMap<Long, Integer> hm;
    DownloadManager downloadmanager;
    BroadcastReceiver catImagereceiver;
    long enqueue;
    DataBaseHandler dbHandler;
    String url = null;
    private String fileName, externalStoragePath;
    private TutorialResponseBean tutorialResponseBean;
    private int catogarySelectedPage;
    private int productSelectePage;

    public TutorialProductDetailsAdapter(final TutorialResponseBean tutorialResponseBean, final Context mContext, final int catogarySelectedPage, final int productSelectePage) {
//        this.documentsList = docList;
        this.mContext = mContext;
        this.tutorialResponseBean = tutorialResponseBean;
        this.catogarySelectedPage = catogarySelectedPage;
        this.productSelectePage = productSelectePage;
//        addTutorials(catogarySelectedPage, productSelectePage);
        hm = new HashMap<Long, Integer>();

        downloadmanager = (DownloadManager) mContext.getSystemService(Context.DOWNLOAD_SERVICE);
        dbHandler = DataBaseHandler.getInstance(mContext);
        /*catImagereceiver = new BroadcastReceiver() {
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
                        if (DownloadManager.STATUS_SUCCESSFUL == c.getInt(columnIndex)) {
                            final int pos = hm.get(enqueue);
//                            tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(pos).setIsDownloaded(true);
//                            DataBaseHandler.getInstance(context).deleteAllTutorialCategory();
//                            DataBaseHandler.getInstance(context).addGetData(new Gson().toJson(tutorialResponseBean), DataBaseHandler.TYPE_TUTORIAL_CATEGORY);
                            openDialog(tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(pos));
                        }
                    }
                } else if (DownloadManager.ACTION_NOTIFICATION_CLICKED.equals(action)) {
                    Intent i = new Intent(DownloadManager.ACTION_VIEW_DOWNLOADS);
//                    i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    mContext.startActivity(i);
                }
            }
        };

        IntentFilter intentFilter = new IntentFilter();
        intentFilter.addAction(DownloadManager.ACTION_DOWNLOAD_COMPLETE);
//        intentFilter.addAction(DownloadManager.ACTION_NOTIFICATION_CLICKED);

        mContext.registerReceiver(catImagereceiver, intentFilter);*/
    }

    /*private void addTutorials(int catogarySelectedPage, int productSelectePage) {
        documentsList = new ArrayList<>();
        for (int i = 0; i < tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo_count(); i++)
            documentsList.add(tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(i));
    }*/

    @Override
    public DocumentViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.tutorial_product_element, parent, false); //Inflating the layout
        DocumentViewHolder vhItem = new DocumentViewHolder(v); //Creating ViewHolder and passing the object of type view
        return vhItem;
    }

    @Override
    public void onBindViewHolder(final DocumentViewHolder holder, int position) {
        final TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo tutorialsVideo = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(position);
        holder.tvDocName.setText(tutorialsVideo.getTitle());
        holder.tvChannel.setText(tutorialsVideo.getChannel_name());
        holder.tvDuration.setText(tutorialsVideo.getDuration());
        if (tutorialsVideo.getThumbnail() != null && !tutorialsVideo.getThumbnail().isEmpty())
            Picasso.with(mContext).load(tutorialsVideo.getThumbnail()).into(holder.ivDocIcon);
        else
            holder.ivDocIcon.setImageResource(R.drawable.no_image);


        if (tutorialsVideo.getVideo_path() != null && !tutorialsVideo.getVideo_path().isEmpty()) {
            AsyncTask.execute(new Runnable() {
                @Override
                public void run() {
                    try {
                        URL url = new URL(NetworkConstants.BASE_URL + "/" + tutorialsVideo.getVideo_path());
                        URLConnection urlConnection = url.openConnection();
                        urlConnection.connect();
                        holder.file_size = urlConnection.getContentLength();
                    } catch (MalformedURLException e) {
                        e.printStackTrace();
                    } catch (IOException e) {
                        e.printStackTrace();
                    }

                    ((TutorialProductDetailActivity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            String fileName[] = tutorialsVideo.getVideo_path().split("/");
                            String fName = fileName[fileName.length - 1];
                            File file = new File(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + fName);
                            if (file.exists() && file.length() > 0) {
                                if (file.length() >= holder.file_size && holder.file_size > 0) {
                                    holder.ivDocPlay.setVisibility(View.VISIBLE);
                                    holder.ivDocDownload.setVisibility(View.GONE);
                                } else {
                                    holder.ivDocPlay.setVisibility(View.GONE);
                                    holder.ivDocDownload.setVisibility(View.VISIBLE);
                                }
                            } else {
                                holder.ivDocPlay.setVisibility(View.GONE);
                                holder.ivDocDownload.setVisibility(View.VISIBLE);
                            }
                        }
                    });

                }
            });
        } else {
            holder.ivDocDownload.setVisibility(View.GONE);
            holder.ivDocPlay.setVisibility(View.GONE);
        }
    }


    @Override
    public int getItemCount() {
        int count = 0;
        if (tutorialResponseBean.getData() != null && tutorialResponseBean.getData().size() > 0 &&
                tutorialResponseBean.getData().get(catogarySelectedPage).getProduct() != null &&
                tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().size() > 0) {
            count = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo_count();
        } else
            count = 0;
        return count;
    }

    /*@Override
    public void unregister() {
        try {
            mContext.unregisterReceiver(catImagereceiver);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }*/

    public class DocumentViewHolder extends RecyclerView.ViewHolder {
        private TextView tvDocName, tvChannel, tvDuration;
        private ImageView ivDocIcon;
        private FontImageView ivDocDownload, ivDocPlay;
        private int file_size;

        public DocumentViewHolder(View itemView) {
            super(itemView);
            //for name of video
            tvDocName = (TextView) itemView.findViewById(R.id.tvDocName);
            tvDocName.setTypeface(Util.getRoboMedium(mContext));
            //for last updated time of video
            tvChannel = (TextView) itemView.findViewById(R.id.tvchannel_name);
            tvChannel.setTypeface(Util.getRoboMedium(mContext));
            //for duration of video
            tvDuration = (TextView) itemView.findViewById(R.id.tvDuration);
            tvDuration.setTypeface(Util.getRoboMedium(mContext));

            // for video thumbnail
            ivDocIcon = (ImageView) itemView.findViewById(R.id.ivDocIcon);
            ivDocPlay = (FontImageView) itemView.findViewById(R.id.iv_doc_play);
            ivDocPlay.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    /*getVideoName();
                    Util.createToast(mContext, "Playinggggggggggggggggggggggggggggg");*/
                    TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo tutorialsVideo = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(getPosition());
                    String fileName[] = tutorialsVideo.getVideo_path().split("/");
                    String fName = fileName[fileName.length - 1];
                    File file = new File(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + fName);
                    if (file.exists() && file.length() > 0) {
                        if (file.length() >= file_size && file_size > 0) {
                            openFileIntent(tutorialsVideo);
                        } else {
                            DownloadManager.Query query = new DownloadManager.Query();
                            String videoQueueIds = DataStore.getVideoQueueIds(mContext);
                            if (!videoQueueIds.isEmpty()) {
                                String[] videoIds = videoQueueIds.split(",");
                                for (int j = 0; j < videoIds.length; j++) {
                                    query.setFilterById(Long.parseLong(videoIds[j]));
                                    Cursor c = downloadmanager.query(query);
                                    if (c.moveToFirst()) {
                                        int status = c.getInt(c.getColumnIndex(DownloadManager.COLUMN_STATUS));
                                        String filePath = c.getString(c.getColumnIndex(DownloadManager.COLUMN_URI));
                                        String filename = filePath.substring(filePath.lastIndexOf('/') + 1, filePath.length());
                                        if (filename.equalsIgnoreCase(fName)) {
                                            if (status == DownloadManager.STATUS_FAILED || status == DownloadManager.STATUS_PAUSED ||
                                                    status == DownloadManager.STATUS_PENDING) {
                                                deleteVideoId(mContext, videoQueueIds, videoIds[j]);
                                                downloadmanager.remove(Long.parseLong(videoIds[j]));
                                                Util.createToast(mContext, "Video file doesn't exist, Please download first");
                                                ivDocDownload.setVisibility(View.VISIBLE);
                                                ivDocPlay.setVisibility(View.GONE);
                                            } else if (status == DownloadManager.STATUS_RUNNING) {
                                                Util.createToast(mContext, "Downloading is going on");
                                            } else if (status == DownloadManager.STATUS_SUCCESSFUL) {
                                                if (file.length() >= file_size && file_size > 0)
                                                    openFileIntent(tutorialsVideo);
                                            }
                                            break;
                                        }
                                    }
                                }

                            } else {
                                if (file.length() >= file_size && file_size > 0)
                                    openFileIntent(tutorialsVideo);
                                else {
                                    Util.createToast(mContext, "Video file doesn't exist, Please download first");
                                    ivDocDownload.setVisibility(View.VISIBLE);
                                    ivDocPlay.setVisibility(View.GONE);
                                }
                            }

//                            Util.createToast(mContext, "Downloading is going on");
                        }
                    } else {
                        Util.createToast(mContext, "Video file doesn't exist, Please download first");
                        ivDocDownload.setVisibility(View.VISIBLE);
                        ivDocPlay.setVisibility(View.GONE);
//                        tutorialsVideo.setIsDownloaded(false);
                    }
                }

                /*private void getVideoName() {
                    String filePath = documentsList.get(getPosition()).getVideo_path();
                    fileName = new String();
                    if (filePath.length() > 0 && filePath.contains("/")) {
                        int index = filePath.lastIndexOf("/");
                        fileName = filePath.substring((index + 1), (filePath.length() - 4));
                        externalStoragePath = new String();
                        externalStoragePath = Environment.getExternalStorageDirectory().getAbsolutePath().toString();

                        // if (Environment.getDownloadCacheDirectory().)
                        Util.createToast(mContext, "Playinggggggggggggggggggggggggggggg");
                        *//*else
                            Util.createToast(mContext,"hellooooo");
*//*
                    }
                }*/
            });

            ivDocDownload = (FontImageView) itemView.findViewById(R.id.iv_doc_download);
            ivDocDownload.setVisibility(View.VISIBLE);
            ivDocPlay.setVisibility(View.GONE);

            ivDocIcon.setClickable(true);
            ivDocIcon.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo tutorialsVideo = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(getPosition());
                    if (Util.isNetworkAvailable(mContext)) {
                        if (tutorialsVideo.getYoutube_url() != null && !tutorialsVideo.getYoutube_url().isEmpty() && tutorialsVideo.getYoutube_url().startsWith("http"))
                            Util.openUrl(mContext, tutorialsVideo.getYoutube_url());
                        else
                            Util.createToast(mContext, "Video not exist");
                    } else
                        Util.createToast(mContext, "Please check network connection");

                }


            });
            ivDocDownload.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo tutorialsVideo = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(getPosition());
                    if (Util.isNetworkAvailable(mContext)) {
                        if ((!tutorialsVideo.getVideo_path().equals(""))) {
/*
                  //      Picasso.with(mContext).load(NetworkConstants.BASE_URL +documentsList.get(getPosition()).getVideo_path());
                      Util.openUrl(mContext, NetworkConstants.BASE_URL + documentsList.get(getPosition()).getVideo_path());
                        ivDocDownload.setVisibility(View.GONE);
                        ivDocPlay.setVisibility(View.VISIBLE);
*/
                            /*String fileName[] = tutorialsVideo.getVideo_path().split("/");
                            String fName = fileName[fileName.length - 1];
                            File file = new File(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + fName);
                            if (file.exists())
                                file.delete();*/
                            fileDownload(getPosition());
//                        ivDocDownload.setEnabled(false);
                            ivDocDownload.setVisibility(View.GONE);
                            ivDocPlay.setVisibility(View.VISIBLE);
                        } else {
                            Util.createToast(mContext, "Video not available");
                        }
                    } else
                        Util.createToast(mContext, "Please check network connection");
                }
            });
        }
    }

    public void displayView(int position) {
        /*if (documentsList.get(position).getVideo_path().equals(""))
            fileDownload(position);
        else {
            openFileIntent(documentsList.get(position));
        }*/
        fileDownload(position);
    }

    private void displayFile(int position) {
        Intent intent = new Intent(mContext, ShowFileView.class);
        //   intent.putExtra("document", documentsList.get(position));
        mContext.startActivity(intent);
    }

    public void fileDownload(int i) {
        boolean isDownload = true;
        TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo document = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(i);
        File direct = new File(Environment.getExternalStorageDirectory()
                + NetworkConstants.hideFolderFromGallery + "GioneeStar");

        if (!direct.exists()) {
            direct.mkdirs();
        }

        try {
            url = NetworkConstants.BASE_URL + "/" + document.getVideo_path();
            Uri downloadUri = Uri.parse(url);

            String fileName[] = document.getVideo_path().split("/");
            String fName = fileName[fileName.length - 1];
            /*DownloadManager.Request request = new DownloadManager.Request(
                    downloadUri);
            request.setAllowedNetworkTypes(
                    DownloadManager.Request.NETWORK_WIFI
                            | DownloadManager.Request.NETWORK_MOBILE)
                    .setAllowedOverRoaming(false)
                    .setTitle("GioneeStar")
                    .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", fName);*/

            DownloadManager.Query query = new DownloadManager.Query();
            String videoQueueIds = DataStore.getVideoQueueIds(mContext);
            if (!videoQueueIds.isEmpty()) {
                String[] videoIds = videoQueueIds.split(",");
                for (int j = 0; j < videoIds.length; j++) {
                    query.setFilterById(Long.parseLong(videoIds[j]));
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int status = c.getInt(c.getColumnIndex(DownloadManager.COLUMN_STATUS));
                        String filePath = c.getString(c.getColumnIndex(DownloadManager.COLUMN_URI));
                        String filename = filePath.substring(filePath.lastIndexOf('/') + 1, filePath.length());
                        if (filename.equalsIgnoreCase(fName)) {
                            if (status == DownloadManager.STATUS_FAILED || status == DownloadManager.STATUS_PAUSED ||
                                    status == DownloadManager.STATUS_PENDING) {
                                deleteVideoId(mContext, videoQueueIds, videoIds[j]);
                                downloadmanager.remove(Long.parseLong(videoIds[j]));
                                startDownloading(downloadUri, i, fName);
                            } else if (status == DownloadManager.STATUS_RUNNING) {
                                Util.createToast(mContext, "Downloading is going on");
                            } else if (status == DownloadManager.STATUS_SUCCESSFUL) {
                                Util.createToast(mContext, "Download completed successfully");
                            }
                            isDownload = true;
                            break;
                        } else {
                            isDownload = false;
//                            startDownloading(downloadUri, i, fName);
                        }
                    } else {
                        isDownload = false;
                        deleteVideoId(mContext, videoQueueIds, videoIds[j]);
//                        fileDownload(i);
//                        startDownloading(downloadUri, i, fName);
                    }
                }
                if (!isDownload)
                    startDownloading(downloadUri, i, fName);

            } else {
                startDownloading(downloadUri, i, fName);
            }

            /*enqueue = downloadmanager.enqueue(request);
            String videosIds = DataStore.getVideoQueueIds(mContext);
            if (videosIds.isEmpty())
                videosIds = videosIds + enqueue;
            else
                videosIds = videosIds + "," + enqueue;
            DataStore.setVideoQueueIds(mContext, videosIds);
            hm.put(enqueue, i);
            Util.createToast(mContext, "Download starts");*/

            //   enqueue = i;
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void startDownloading(Uri downloadUri, int i, String fName) {
        File file = new File(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + fName);
        if (file.exists())
            file.delete();
        DownloadManager.Request request = new DownloadManager.Request(
                downloadUri);
        request.setAllowedNetworkTypes(
                DownloadManager.Request.NETWORK_WIFI
                        | DownloadManager.Request.NETWORK_MOBILE)
                .setAllowedOverRoaming(false)
                .setTitle("GioneeStar")
                .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", fName);
        enqueue = downloadmanager.enqueue(request);
        String videosIds = DataStore.getVideoQueueIds(mContext);
        if (videosIds.isEmpty())
            videosIds = videosIds + enqueue;
        else
            videosIds = videosIds + "," + enqueue;
        DataStore.setVideoQueueIds(mContext, videosIds);
        hm.put(enqueue, i);
        Util.createToast(mContext, "Download starts");
    }

    private void deleteVideoId(Context context, String videoQueueIds, String videoId) {
        String[] videoIds1 = videoQueueIds.split(",");
        List<String> list = new ArrayList<String>(Arrays.asList(videoIds1));
        list.remove(videoId);
        videoIds1 = list.toArray(new String[0]);
        if (videoIds1.length > 0) {
            String id = "";
            for (int i = 0; i < videoIds1.length; i++) {
                if (i == 0)
                    id = id + videoIds1[i];
                else
                    id = id + "," + videoIds1[i];
            }
            DataStore.setVideoQueueIds(context, id);
        } else {
            DataStore.setVideoQueueIds(context, "");
        }
    }

    private void openDialog(final TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo doc) {
        final Dialog dialog = new Dialog(mContext);
        dialog.setCancelable(false);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.file_open_dialog);
        dialog.getWindow().setLayout(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        TextView tvHeader = (TextView) dialog.findViewById(R.id.tv_header);
        tvHeader.setTypeface(Util.getRoboMedium(mContext));
        tvHeader.setText("Successfully downloaded file " + doc.getTitle());
        TextView tvMessage = (TextView) dialog.findViewById(R.id.tv_message);
        tvMessage.setTypeface(Util.getRoboRegular(mContext));
        tvMessage.setText("Do you want to open " + doc.getTitle());
        TextView tvOk = (TextView) dialog.findViewById(R.id.tvYes);
        tvOk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                openFileIntent(doc);
                dialog.dismiss();
            }
        });
        TextView tvCancel = (TextView) dialog.findViewById(R.id.tvNo);
        tvCancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });
        dialog.show();

    }

    private void openFileIntent(TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo doc) {
        try {
            String fileName[] = doc.getVideo_path().split("/");
            String fName = fileName[fileName.length - 1];
            File file = new File(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + fName);
            Uri path = Uri.fromFile(file);
            Intent fileOpenintent = null;
            fileOpenintent = new Intent(Intent.ACTION_VIEW);
            fileOpenintent.setDataAndType(path, "video/mpeg");
            if (fileOpenintent != null) {
                fileOpenintent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                try {
                    mContext.startActivity(fileOpenintent);
                } catch (ActivityNotFoundException e) {
                    e.printStackTrace();
                }
            }
        } catch (Exception e) {
        }
    }

}
