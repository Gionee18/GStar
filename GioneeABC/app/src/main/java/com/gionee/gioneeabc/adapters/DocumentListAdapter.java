package com.gionee.gioneeabc.adapters;

import android.app.Dialog;
import android.app.DownloadManager;
import android.content.ActivityNotFoundException;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.net.Uri;
import android.os.Environment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ImageViewActivity;
import com.gionee.gioneeabc.activities.ShowFileView;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.ProductVaultFragment;
import com.gionee.gioneeabc.helpers.FontImageView;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.Util;

import java.io.File;
import java.util.HashMap;
import java.util.List;

/**
 * Created by Linchpin25 on 3/2/2016.
 */

public class DocumentListAdapter extends RecyclerView.Adapter<DocumentListAdapter.DocumentViewHolder> implements ProductVaultFragment.UnRegisterReceiver {
    List<DocumentBean> documentsList;
    Context mContext;
    HashMap<Long, Integer> hm;
    DownloadManager downloadmanager;
    BroadcastReceiver catImagereceiver;
    long enqueue;
    DataBaseHandler dbHandler;
    String url = null;

    public DocumentListAdapter(List<DocumentBean> docList, Context mContext) {
        this.documentsList = docList;
        this.mContext = mContext;
        hm = new HashMap<Long, Integer>();

        downloadmanager = (DownloadManager) mContext.getSystemService(Context.DOWNLOAD_SERVICE);
        dbHandler = DataBaseHandler.getInstance(mContext);
        catImagereceiver = new BroadcastReceiver() {
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
                            dbHandler.updateDocumentLocalPath(documentsList.get(pos).getDocId(), Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" +
                                    NetworkConstants.hideImageFromGallery + documentsList.get(pos).getDocName());
                            notifyDataSetChanged();
                            documentsList.get(pos).setDocLocalPath(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + documentsList.get(pos).getDocName());
                            openDialog(documentsList.get(pos));
                        }
                    }
                }
            }
        };


        mContext.registerReceiver(catImagereceiver, new IntentFilter(
                DownloadManager.ACTION_DOWNLOAD_COMPLETE));
    }

    @Override
    public DocumentViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.document_item, parent, false); //Inflating the layout
        DocumentViewHolder vhItem = new DocumentViewHolder(v); //Creating ViewHolder and passing the object of type view
        return vhItem;
    }

    @Override
    public void onBindViewHolder(DocumentViewHolder holder, int position) {
        if (documentsList.get(position).getDocType().equals("PDF")) {
            holder.ivDocIcon.setText("q");
        } else if (documentsList.get(position).getDocType().equals("TXT")||documentsList.get(position).getDocType().equals("DOCX")) {
            holder.ivDocIcon.setText("o");
        } else if (documentsList.get(position).getDocType().equals("DOC")) {
            holder.ivDocIcon.setText("u");
        } else if (documentsList.get(position).getDocType().equals("VID")) {
            holder.ivDocIcon.setText("v");
        } else if (documentsList.get(position).getDocType().equals("JPG") ||documentsList.get(position).getDocType().equals("GIF")|| documentsList.get(position).getDocType().equals("PNG")) {
            holder.ivDocIcon.setText("p");
        } else if (documentsList.get(position).getDocType().equals("XLS") || documentsList.get(position).getDocType().equals("XLSX") || documentsList.get(position).getDocType().equals("ODS")) {
            holder.ivDocIcon.setText("n");
        } else if (documentsList.get(position).getDocType().equals("3GP") || documentsList.get(position).getDocType().equals("MP4")) {
            holder.ivDocIcon.setText("v");
        }
        holder.tvDocName.setText(documentsList.get(position).getDocTitle());
        if (documentsList.get(position).getDocLocalPath().equals(""))
            holder.ivDocDownload.setText("m");
        /*else
            holder.ivDocDownload.setText("s");*/
    }


    @Override
    public int getItemCount() {
        return documentsList.size();
    }

    @Override
    public void unregister() {
        try {
            mContext.unregisterReceiver(catImagereceiver);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public class DocumentViewHolder extends RecyclerView.ViewHolder {
        TextView tvDocName;
        FontImageView ivDocIcon, ivDocDownload, ivDocView;

        public DocumentViewHolder(View itemView) {
            super(itemView);
            tvDocName = (TextView) itemView.findViewById(R.id.tvDocName);
            tvDocName.setTypeface(Util.getRoboMedium(mContext));
            ivDocIcon = (FontImageView) itemView.findViewById(R.id.ivDocIcon);
            ivDocDownload = (FontImageView) itemView.findViewById(R.id.iv_doc_download);
            ivDocView = (FontImageView) itemView.findViewById(R.id.iv_doc_view);
            ivDocDownload.setClickable(true);
            ivDocView.setClickable(true);
            ivDocDownload.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (Util.isNetworkAvailable(mContext) ||
                            (!documentsList.get(getPosition()).getDocLocalPath().equals("")))
                        displayView(getPosition());
                    else
                        Util.createToast(mContext, "Please check network connection");

                }
            });
            ivDocView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (Util.isNetworkAvailable(mContext))
                        displayFile(getPosition());
                    else
                        Util.createToast(mContext, "Please check network connection");
                }
            });
        }
    }

    public void displayView(int position) {
        if (documentsList.get(position).getDocLocalPath().equals(""))
            fileDownload(position);
        else {
            openFileIntent(documentsList.get(position));
        }
    }

    private void displayFile(int position) {
        Intent intent = new Intent(mContext, ShowFileView.class);
        intent.putExtra("document", documentsList.get(position));
        mContext.startActivity(intent);
    }

    public void fileDownload(int i) {
        DocumentBean document = documentsList.get(i);
        File direct = new File(Environment.getExternalStorageDirectory()
                + NetworkConstants.hideFolderFromGallery + "GioneeStar");

        if (!direct.exists()) {
            direct.mkdirs();
        }

        try {
            url = NetworkConstants.BASE_URL + "/" + document.getDocUrl() + "/" + document.getDocName();
            Uri downloadUri = Uri.parse(url);
            DownloadManager.Request request = new DownloadManager.Request(
                    downloadUri);
            request.setAllowedNetworkTypes(
                    DownloadManager.Request.NETWORK_WIFI
                            | DownloadManager.Request.NETWORK_MOBILE)
                    .setAllowedOverRoaming(false)
                    .setTitle("GioneeStar")
                    .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", document.getDocName());

            enqueue = downloadmanager.enqueue(request);
            hm.put(enqueue, i);
            Util.createToast(mContext, "Download starts");
            //   enqueue = i;
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void openDialog(final DocumentBean doc) {
        final Dialog dialog = new Dialog(mContext);
        dialog.setCancelable(false);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.file_open_dialog);
        dialog.getWindow().setLayout(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        TextView tvHeader = (TextView) dialog.findViewById(R.id.tv_header);
        tvHeader.setTypeface(Util.getRoboMedium(mContext));
        tvHeader.setText("Successfully downloaded file " + doc.getDocTitle());
        TextView tvMessage = (TextView) dialog.findViewById(R.id.tv_message);
        tvMessage.setTypeface(Util.getRoboRegular(mContext));
        tvMessage.setText("Do you want to open " + doc.getDocTitle());
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

    private void openFileIntent(DocumentBean doc) {
        File file = new File(doc.getDocLocalPath());
        Uri path = Uri.fromFile(file);
        Intent fileOpenintent = null;
        switch (doc.getDocType()) {
            case "PDF":
            case "DOCX":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/pdf");
                break;
            case "TXT":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/msword");
                break;
            case "3GP":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "video/mpeg");
                break;
            case "MP4":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "video/mpeg");
                break;
            case "JPG":
                openImage(doc.getDocLocalPath());
                break;
            case "PNG":
            case "GIF":
                openImage(doc.getDocLocalPath());
                break;
            case "DOC":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/msword");
                break;

            case "XLS":
            case "XLSX":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/vnd.ms-excel");
                break;
        }
        if (fileOpenintent != null) {
            fileOpenintent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            try {
                mContext.startActivity(fileOpenintent);
            } catch (ActivityNotFoundException e) {
                e.printStackTrace();

            }
        }
    }

    private void openImage(String filePath) {
        mContext.startActivity(new Intent(mContext, ImageViewActivity.class).putExtra("imagePath", filePath));
    }


}
