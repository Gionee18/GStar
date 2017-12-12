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
import com.gionee.gioneeabc.activities.TutorialProductDetailActivity;
import com.gionee.gioneeabc.activities.TutorialProductsActivity;
import com.gionee.gioneeabc.adapters.TutorialProductDetailsAdapter;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.TutorialResponseBean;
import com.gionee.gioneeabc.database.DataBaseHandler;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

/**
 * Created by root on 4/10/16.
 */
public class TutorialProductDetailsFragment extends Fragment {
    View rootView;
    DataBaseHandler dbHandler;
    ProductBean product;
    RecyclerView.Adapter adapter;
    private ArrayList<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean.TutorialDataProductTutorials.TutorialDataProductTutorialsVideo> tutorialsArrayList;
    RecyclerView recyclerView;
    TextView tvNoDocFound;
    String voultJson;
    NestedScrollView nestedScrollView;
    private int catogarySelectedPage, productSelectePage;
    private UnRegisterReceiver unRegisterReceiver;
    private TutorialResponseBean tutorialResponseBean;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        rootView = inflater.inflate(R.layout.product_document_screen, null);
        catogarySelectedPage = TutorialProductsActivity.selectedPage;
        productSelectePage = TutorialProductDetailActivity.pos;
        tutorialResponseBean = TutorialProductsActivity.tutorialDataCatogaryBean;
        addTutorials();
        //dbHandler = DataBaseHandler.getInstance(getActivity());
        nestedScrollView = (NestedScrollView) rootView.findViewById(R.id.nestedScrollView);
        recyclerView = (RecyclerView) rootView.findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));

        try {
            getDataFromLocal(((TutorialProductDetailActivity) getActivity()).product.getPro_image().toString());
        } catch (Exception e) {
        }
        /*voultJson=((TutorialProductDetailActivity) getActivity()).product.getVaultDocsJson();
        parseVoultJson(voultJson);*/
        tvNoDocFound = (TextView) rootView.findViewById(R.id.tvNoDoc);


        //documentList = ((ProductDetailActivity) getActivity()).documentList;

        if (tutorialsArrayList != null && tutorialsArrayList.size() > 0) {
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

    private void addTutorials() {
        tutorialsArrayList = new ArrayList<>();
        if (tutorialResponseBean.getData() != null && tutorialResponseBean.getData().size() > 0) {
            List<TutorialResponseBean.TutorialDataCatogaryBean.TutorialDataProductBean> tutorialDataProductBeanList = tutorialResponseBean.getData().get(catogarySelectedPage).getProduct();
            if (tutorialDataProductBeanList != null && tutorialDataProductBeanList.size() > 0 &&
                    tutorialDataProductBeanList.get(productSelectePage).getTutorials().getVideo() != null &&
                    tutorialDataProductBeanList.get(productSelectePage).getTutorials().getVideo().size() > 0) {
                for (int i = 0; i < tutorialDataProductBeanList.get(productSelectePage).getTutorials().getVideo_count(); i++)
                    tutorialsArrayList.add(tutorialDataProductBeanList.get(productSelectePage).getTutorials().getVideo().get(i));
            }
        }
    }


    private void setAdapter() {
        adapter = new TutorialProductDetailsAdapter(tutorialResponseBean, getActivity(), catogarySelectedPage, productSelectePage);
        recyclerView.setAdapter(adapter);
        // addDocumentsIntoDatabase();
        // unRegisterReceiver=(UnRegisterReceiver)adapter;
    }

   /* private void addDocumentsIntoDatabase() {
        for (int i = 0; i < documentList.size(); i++) {
            dbHandler.addDocument(documentList.get(i));
        }
    }*/

    @Override
    public void onDestroy() {
        super.onDestroy();
        try {
            unRegisterReceiver.unregister();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public interface UnRegisterReceiver {
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
                    //  documentList.add(document);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void parseVoultJson(String json) {
        JSONArray child1 = null;
        try {
            if (json != null && !json.equals("")) {
                child1 = new JSONArray(json);
                for (int k = 0; k < child1.length(); k++) {
                    JSONObject child = child1.getJSONObject(k);
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
                    //   documentList.add(document);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
