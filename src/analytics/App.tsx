import { useEffect, useMemo, useState } from "react";
import { AlertCircle, AlertTriangle, ArrowRight, Loader2, Smile } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/components/ui/chart";
import { Area, AreaChart, CartesianGrid, Legend, Tooltip, XAxis, YAxis } from "recharts@2.15.2";
import { cn } from "@/components/ui/utils";
import { sampleOverview } from "./sample-data";
import type { ApiOverviewResponse } from "./types";

const chartPalette = {
  delivered: { label: "Delivered", color: "#5b8def" },
  opened: { label: "Opened", color: "#22d3ee" },
  clicked: { label: "Clicked", color: "#34d399" },
  completed: { label: "Completed", color: "#facc15" },
} as const;

export default function AnalyticsApp() {
  const [data, setData] = useState<ApiOverviewResponse>(sampleOverview);
  const [loading, setLoading] = useState<boolean>(Boolean(import.meta.env.VITE_ANALYTICS_ENDPOINT));
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const endpoint = import.meta.env.VITE_ANALYTICS_ENDPOINT;
    if (!endpoint) {
      setLoading(false);
      return;
    }

    let cancelled = false;

    const fetchOverview = async () => {
      setLoading(true);
      try {
        const response = await fetch(endpoint);
        if (!response.ok) {
          throw new Error(`Request failed with status ${response.status}`);
        }
        const json = (await response.json()) as ApiOverviewResponse;
        if (!cancelled) {
          setData(json);
          setError(null);
        }
      } catch (err) {
        if (!cancelled) {
          setError(
            err instanceof Error
              ? err.message
              : "Could not load live analytics data. Showing sample metrics instead.",
          );
          setData(sampleOverview);
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    };

    fetchOverview();

    return () => {
      cancelled = true;
    };
  }, []);

  const totals = useMemo(() => {
    return [...data.totals].sort((a, b) => b._count - a._count);
  }, [data]);

  const sentimentBreakdown = useMemo(() => {
    const lookup: Record<string, number> = { positive: 0, neutral: 0, negative: 0 };
    for (const bucket of data.sentiment) {
      if (!bucket.sentiment) continue;
      const key = bucket.sentiment.toLowerCase();
      lookup[key] = (lookup[key] ?? 0) + bucket._count;
    }
    return [
      { label: "Positive", count: lookup.positive ?? 0, className: "text-emerald-400" },
      { label: "Neutral", count: lookup.neutral ?? 0, className: "text-slate-300" },
      { label: "Negative", count: lookup.negative ?? 0, className: "text-rose-400" },
    ];
  }, [data]);

  return (
    <main className="min-h-screen bg-slate-950 pb-16 text-slate-50">
      <div className="mx-auto flex w-full max-w-7xl flex-col gap-10 px-6 pt-12">
        <header className="flex flex-col gap-2">
          <div className="flex items-center gap-3">
            <span className="text-sm uppercase tracking-[0.3em] text-sky-300">
              Therapair Research
            </span>
            {loading ? (
              <span className="flex items-center gap-1 rounded-full bg-sky-500/10 px-2 py-1 text-xs text-sky-200">
                <Loader2 className="h-3.5 w-3.5 animate-spin" />
                Syncing live data…
              </span>
            ) : (
              <span className="rounded-full bg-emerald-500/10 px-2 py-1 text-xs text-emerald-200">
                Static sample data
              </span>
            )}
          </div>
          <h1 className="text-4xl font-semibold">Victoria therapist research dashboard</h1>
          <p className="max-w-2xl text-sm text-slate-300">
            Real-time view of outreach performance, survey conversion, and qualitative feedback.
          </p>
        </header>

        {error ? (
          <div className="flex items-center gap-3 rounded-md border border-amber-500/40 bg-amber-500/10 p-4 text-sm text-amber-100">
            <AlertCircle className="h-4 w-4 flex-none" />
            <p>
              {error} The dashboard is currently using the built-in sample dataset. Set{" "}
              <code className="bg-slate-900 px-1 py-0.5 text-[11px] text-slate-200">
                VITE_ANALYTICS_ENDPOINT
              </code>{" "}
              to load live data again.
            </p>
          </div>
        ) : null}

        <section className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          {totals.map((metric) => (
            <Card key={metric.eventType} className="border-slate-800 bg-slate-900/70">
              <CardHeader>
                <CardTitle className="text-xs uppercase tracking-wide text-slate-400">
                  {metric.eventType.toLowerCase().replace(/_/g, " ")}
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-3xl font-semibold text-slate-50">
                  {metric._count.toLocaleString("en-AU")}
                </p>
              </CardContent>
            </Card>
          ))}
        </section>

        <section className="grid gap-6 lg:grid-cols-[2fr,1fr]">
          <Card className="border-slate-800 bg-slate-900/70">
            <CardHeader>
              <CardTitle className="text-lg">Email &amp; survey momentum</CardTitle>
            </CardHeader>
            <CardContent>
              <ChartContainer config={chartPalette}>
                <AreaChart
                  data={data.daily}
                  margin={{ left: 12, right: 12, top: 16, bottom: 0 }}
                >
                  <CartesianGrid stroke="rgba(148, 163, 184, 0.15)" />
                  <XAxis
                    dataKey={(row) =>
                      new Date(row.date).toLocaleDateString("en-AU", {
                        month: "short",
                        day: "numeric",
                      })
                    }
                    stroke="#94a3b8"
                    fontSize={12}
                  />
                  <YAxis stroke="#94a3b8" fontSize={12} width={48} />
                  <Tooltip content={<ChartTooltipContent />} />
                  <Legend />
                  <Area
                    type="monotone"
                    dataKey="emailDelivered"
                    stroke="var(--color-delivered)"
                    fill="var(--color-delivered)"
                    fillOpacity={0.2}
                    name="Delivered"
                  />
                  <Area
                    type="monotone"
                    dataKey="emailOpened"
                    stroke="var(--color-opened)"
                    fill="var(--color-opened)"
                    fillOpacity={0.2}
                    name="Opened"
                  />
                  <Area
                    type="monotone"
                    dataKey="emailClicked"
                    stroke="var(--color-clicked)"
                    fill="var(--color-clicked)"
                    fillOpacity={0.2}
                    name="Clicked"
                  />
                  <Area
                    type="monotone"
                    dataKey="surveyCompleted"
                    stroke="var(--color-completed)"
                    fill="var(--color-completed)"
                    fillOpacity={0.2}
                    name="Survey completed"
                  />
                </AreaChart>
              </ChartContainer>
            </CardContent>
          </Card>

          <Card className="flex flex-col justify-between border-slate-800 bg-slate-900/70">
            <CardHeader>
              <CardTitle className="text-lg">Research programme health</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <FunnelList funnel={data.funnel} />
              <SentimentList sentiment={sentimentBreakdown} />
            </CardContent>
          </Card>
        </section>

        <section className="grid gap-6 lg:grid-cols-3">
          <Card className="border-slate-800 bg-slate-900/70 lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-lg">
                <AlertTriangle className="h-4 w-4 text-amber-400" />
                Alerts &amp; red flags
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {data.alerts.map((alert, index) => (
                <div
                  key={`${alert.message}-${index}`}
                  className={cn(
                    "flex items-start gap-3 rounded-md border px-3 py-2 text-sm",
                    alert.type === "warning"
                      ? "border-amber-500/40 bg-amber-500/10 text-amber-200"
                      : "border-sky-500/40 bg-sky-500/10 text-sky-100",
                  )}
                >
                  <AlertTriangle className="mt-0.5 h-4 w-4 flex-none" />
                  <p>{alert.message}</p>
                </div>
              ))}
            </CardContent>
          </Card>

          <Card className="border-slate-800 bg-slate-900/70">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-lg">
                <Smile className="h-4 w-4 text-sky-300" />
                Feedback highlights
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {data.feedbackHighlights.map((feedback) => (
                <article
                  key={feedback.id}
                  className="rounded-lg border border-slate-800 bg-slate-900/70 p-3"
                >
                  <div className="mb-2 flex items-center justify-between">
                    <Badge variant="secondary" className="bg-slate-800/60 text-slate-200">
                      {feedback.sentiment ?? "Unlabelled"}
                    </Badge>
                    <time className="text-xs text-slate-400">
                      {new Date(feedback.createdAt).toLocaleDateString("en-AU", {
                        month: "short",
                        day: "numeric",
                      })}
                    </time>
                  </div>
                  <p className="text-sm text-slate-200">{feedback.text}</p>
                  <div className="mt-2 flex items-center gap-1 text-xs text-slate-400">
                    <ArrowRight className="h-3 w-3" />
                    <span>{feedback.themeCluster ?? feedback.category ?? "General"}</span>
                  </div>
                </article>
              ))}
              {data.feedbackHighlights.length === 0 && (
                <p className="text-sm text-slate-400">
                  We haven’t received fresh feedback in the past fortnight.
                </p>
              )}
            </CardContent>
          </Card>
        </section>
      </div>
    </main>
  );
}

function FunnelList({ funnel }: { funnel: ApiOverviewResponse["funnel"] }) {
  if (!funnel.length) {
    return <p className="text-sm text-slate-400">No funnel data yet.</p>;
  }
  return (
    <ol className="space-y-2 text-sm">
      {funnel.map((step, index) => (
        <li
          key={step.stage}
          className="flex items-center justify-between rounded-md border border-slate-800 bg-slate-900/60 px-3 py-2"
        >
          <span className="font-medium text-slate-200">
            {index + 1}. {step.stage}
          </span>
          <span className="font-mono text-slate-300">{step.count.toLocaleString("en-AU")}</span>
        </li>
      ))}
    </ol>
  );
}

function SentimentList({
  sentiment,
}: {
  sentiment: Array<{ label: string; count: number; className: string }>;
}) {
  return (
    <div className="space-y-2">
      {sentiment.map((bucket) => (
        <div key={bucket.label} className="flex items-center justify-between text-sm">
          <span className={cn("font-medium", bucket.className)}>{bucket.label}</span>
          <span className="font-mono text-slate-200">{bucket.count}</span>
        </div>
      ))}
    </div>
  );
}

