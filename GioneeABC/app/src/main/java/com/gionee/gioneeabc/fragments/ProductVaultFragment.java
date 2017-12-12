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
import com.gionee.gioneeabc.activities.ProductDetailActivity;
import com.gionee.gioneeabc.adapters.DocumentListAdapter;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.database.DataBaseHandler;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class ProductVaultFragment extends Fragment {

    View rootView;
    DataBaseHandler dbHandler;
    ProductBean product;
    RecyclerView.Adapter adapter;
    List<DocumentBean> documentList;
    RecyclerView recyclerView;
    TextView tvNoDocFound;
    String voultJson;
    NestedScrollView nestedScrollView;
    private UnRegisterReceiver unRegisterReceiver;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        rootView = inflater.inflate(R.layout.product_document_screen, null);
        dbHandler = DataBaseHandler.getInstance(getActivity());
        nestedScrollView= (NestedScrollView) rootView.findViewById(R.id.nestedScrollView);
        recyclerView = (RecyclerView) rootView.findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        documentList=new ArrayList<>();
        getDataFromLocal(((ProductDetailActivity) getActivity()).product.getProductImagesJson());
        voultJson=((ProductDetailActivity) getActivity()).product.getVaultDocsJson();
        parseVoultJson(voultJson);
        tvNoDocFound = (TextView) rootView.findViewById(R.id.tvNoDoc);


        //documentList = ((ProductDetailActivity) getActivity()).documentList;

        if (documentList != null && documentList.size() > 0) {
            recyclerView.setVisibility(View.VISIBLE);
            tvNoDocFound.setVisibility(View.GONE);
            nestedScrollView.setVisibility(View.GONE);
            setAdapter();

        } else {
            recyclerView.setVisibility(View.GONE);
            tvNoDocFound.setVisibility(View.VISIBLE);
            nestedScrollView.setVisibility(View.VISIBLE);
        }
        return rootView;
    }


    private void setAdapter() {
        adapter = new DocumentListAdapter(documentList, getActivity());
        recyclerView.setAdapter(adapter);
        addDocumentsIntoDatabase();
        unRegisterReceiver=(UnRegisterReceiver)adapter;
    }

    private void addDocumentsIntoDatabase() {
        for (int i = 0; i < documentList.size(); i++) {
            dbHandler.addDocument(documentList.get(i));
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        try {
            unRegisterReceiver.unregister();
        }catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public interface UnRegisterReceiver
    {
        void unregister();
    }

    private void getDataFromLocal(String json) {
        JSONArray child1 = null;
        try {
            if (json != null && !json.equals("")) {
                child1 = new JSONArray(json);
                for (int k = 0; k < child1.length(); k++) {
                    DocumentBean document = new DocumentBean();
                    JSONObject asset = child1.getJSONObject(k);
                    document.setDocId(asset.optInt("image_id"));
                    document.setDocUrl(asset.optString("path"));
                    document.setDocName(asset.optString("name"));
                    document.setDocTitle(asset.optString("title"));
                    document.setDocLocalPath("");
                   /* document.setDocLocalPath(Environment.getExternalStorageDirectory()
                            + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + document.getDocName());*/
                    String docName = asset.optString("name");
                    if (docName != null && !docName.equals("")) {
                        String[] docTypeArray = docName.split(Pattern.quote("."));

                        document.setDocType(docTypeArray[docTypeArray.length - 1].toUpperCase());

                    } else
                        document.setDocType("OTHER");
                    documentList.add(document);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    private void parseVoultJson(String json) {
        JSONArray child1=null;
        try {
            if(json!=null && !json.equals("")) {
                child1 = new JSONArray(json);
                for (int k = 0; k < child1.length(); k++) {
                    JSONObject child=child1.getJSONObject(k);
                    DocumentBean document = new DocumentBean();
                    document.setDocId(child.optInt("doc_id"));
                    document.setDocLocalPath("");
                    document.setDocTitle(child.optString("title"));

                    document.setDocUrl(child.optString("path"));
                    String docName = child.optString("name");
                    document.setDocName(docName);
                    if (docName != null && !docName.equals("")) {
                        String[] docTypeArray = docName.split(Pattern.quote("."));

                        document.setDocType(docTypeArray[docTypeArray.length - 1].toUpperCase());

                    } else
                        document.setDocType("OTHER");
                    //  document.setProductId(product.getId());
                    documentList.add(document);
                }
            }
        }catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
